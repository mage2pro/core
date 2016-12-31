<?php
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Framework\Controller\Result\Text;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Payment\Model\Method\AbstractMethod as M;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Webhook extends \Df\Core\O {
	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Перекрытие этого метода позволяет потомкам разом задать набор параметров данного класса.
	 * Такая техника является более лаконичным вариантом,
	 * нежели объявление и перекрытие методов для отдельных параметров.
	 * @used-by configCached()
	 * @return array(string => mixed)
	 */
	abstract protected function config();

	/**
	 * 2016-07-10
	 * 2016-12-31
	 * Возвращает идентификатор текущего платежа в платёжной системе.
	 * Этот идентификатор мы используем двояко:
	 * 1) Для последующих запросов к платёжной системе.
	 * 2) Для отображения администратору магазина
	 * (при возможности — с прямой ссылкой на страницу платежа
	 * в личном кабинете магазина в платёжной системе)
	 * @used-by \Dfe\AllPay\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @used-by id()
	 * @return string
	 */
	final public function externalId() {return $this->cv(self::$externalIdKey);}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	final public function handle() {
		/** @var Result $result */
		try {
			$this->handleBefore();
			if ($this->ss()->log()) {
				$this->log();
			}
			$this->validate();
			$this->addTransaction();
			/**
			 * 2016-07-14
			 * Если покупатель не смог или не захотел оплатить заказ,
			 * то мы заказ отменяем, а затем, когда платёжная система возврат покупателя в магазин,
			 * то мы проверим, не отменён ли последний заказ,
			 * и если он отменён — то восстановим корзину покупателя.
			 */
			if (!$this->isSuccessful()) {
				$this->o()->cancel();
			}
			else if ($this->needCapture()) {
				$this->capture();
			}
			$this->o()->save();
			/**
			 * 2016-08-17
			 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/17
			 * Письмо отсылаем только если isSuccessful() вернуло true
			 * (при этом не факт, что оплата уже прошла: при оффлайновом способе оплаты
			 * isSuccessful() говорит лишь о том, что покупатель успешно выбрал оффлайновый способ оплаты,
			 * а подтверждение платежа придёт лишь потом, через несколько дней).
			 */
			if ($this->isSuccessful()) {
				$this->sendEmailIfNeeded();
			}
			$result = $this->resultSuccess();
		}
		catch (\Exception $e) {
			/**
			 * 2016-07-15
			 * Раньше тут стояло
					if ($this->_order) {
						$this->_order->cancel();
						$this->_order->save();
					}
			 * На самом деле, исключительная ситуация свидетельствует о сбое в программе,
			 * либо о некорректном запросе якобы от платёжного сервера (хакерской попытке, например),
			 * поэтому отменять заказ тут неразумно.
			 * В случае сбоя платёжная система будет присылать
			 * повторные оповещения — вот пусть и присылает,
			 * авось мы к тому времени уже починим программу, если поломка была на нашей строне
			 */
			$result = static::resultError($e);
		}
		return $result;
	}

	/**
	 * 2016-07-09
	 * @used-by parentIdG()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @return string
	 */
	final public function parentId() {return $this[$this->parentIdKey()];}

	/**
	 * 2016-07-10
	 * @used-by \Dfe\SecurePay\Signer\Response::req()
	 * @param string|null $key [optional]
	 * @return array(string => string)|string|null
	 */
	final public function parentInfo($key = null) {
		/** @var array(string => string) $info */
		$info = dfc($this, function() {return df_trans_raw_details($this->tParent());});
		return is_null($key) ? $info : dfa($info, $key);
	}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by handle()
	 * @return void
	 * @throws \Exception
	 */
	public function validate() {}

	/**
	 * 2016-07-12
	 * @return void
	 */
	final protected function addTransaction() {
		/**
		 * 2016-08-29
		 * Идентификатор транзакции мы предварительно установили в методе @see ii()
		 */
		$this->m()->applyCustomTransId();
		dfp_set_transaction_info($this->ii(), $this->getData());
		/**
		 * 2016-07-12
		 * @used-by \Magento\Sales\Model\Order\Payment\Transaction\Builder::linkWithParentTransaction()
		 */
		$this->ii()->setParentTransactionId($this->tParent()->getTxnId());
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственная транзакции без специального назначения,
		 * и поэтому мы можем безопасно его использовать.
		 *
		 * 2017-01-01
		 * @uses \Magento\Sales\Model\Order\Payment::addTransaction()
		 * создаёт и настраивает объект-транзакцию, но не записывает её в базу данных,
		 * поэтому если мы далее осуществляем операцию @see capture(),
		 * то там будет использована эта же транзакция, только её тип обновится на
		 * @see \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE
		 * @see \Magento\Sales\Model\Order\Payment\Transaction\Manager::generateTransactionId():
				if (!$payment->getParentTransactionId()
					&& !$payment->getTransactionId() && $transactionBasedOn
		 		) {
					$payment->setParentTransactionId($transactionBasedOn->getTxnId());
				}
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order/Payment/Transaction/Manager.php#L73-L75
		 */
		$this->ii()->addTransaction(T::TYPE_PAYMENT);
	}

	/**
	 * 2016-08-27
	 * @param string|null $key [optional]
	 * @param string|null $d [optional]
	 * @param bool $required [optional]
	 * @return mixed
	 */
	final protected function cv($key = null, $d = null, $required = true) {
		$key = $this->c($key ?: df_caller_f(), $required);
		return !$key || !$this->offsetExists($key) ? $d : $this[$key];
	}

	/**
	 * 2016-12-30
	 * @used-by \Df\Payment\Webhook::log()
	 * @param string|null $key [optional]
	 * @param string|null $d [optional]
	 * @return mixed
	 */
	final protected function cvo($key = null, $d = null) {return
		$this->cv($key ?: df_caller_f(), $d, false)
	;}

	/**
	 * 2016-12-30
	 * @used-by testData()
	 * @see \Df\StripeClone\Webhook::defaultTestCase()
	 * @return string
	 */
	protected function defaultTestCase() {return 'confirm';}

	/**
	 * 2016-07-20
	 * @used-by handle()
	 * @return void
	 */
	protected function handleBefore() {}

	/**
	 * 2016-07-20
	 * @used-by ii()
	 * @see \Dfe\AllPay\Webhook\Offline::id()
	 * @return string
	 */
	protected function id() {return $this->idL2G($this->externalId());}

	/**
	 * 2016-07-20
	 * @used-by handle()
	 * @see \Df\StripeClone\Webhook::needCapture()
	 * @see \Dfe\AllPay\Webhook\BankCard::needCapture()
	 * @see \Dfe\AllPay\Webhook\Offline::needCapture()
	 * @see \Dfe\AllPay\Webhook\WebATM::needCapture()
	 * @return bool
	 */
	protected function needCapture() {return $this->c();}

	/**
	 * 2016-08-29
	 * Потомки перекрывают этот метод, когда ключ идентификатора запроса в запросе
	 * не совпадает с ключем идентификатора запроса в ответе.
	 * Так, в частности, происходит в модуле SecurePay:
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Webhook::parentIdKey()
	 *
	 * @uses \Df\PaypalClone\ICharge::requestIdKey()
	 * @used-by requestId()
	 * @return string
	 */
	protected function parentIdKey() {return df_con_s($this, 'Charge', 'requestIdKey');}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @see \Dfe\AllPay\Webhook::resultSuccess()
	 * @return Result
	 */
	protected function resultSuccess() {return Text::i('success');}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @see \Dfe\AllPay\Webhook::$statusExpected()
	 * @see \Dfe\AllPay\Webhook\Offline::$statusExpected
	 * @return string|int
	 */
	protected function statusExpected() {return $this->c();}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	final protected function store() {return $this->o()->getStore();}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @return string
	 */
	final protected function type() {return $this->cv(self::$typeKey, 'confirmation');}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @see \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	protected function typeLabel() {return $this->type();}

	/**
	 * 2016-08-27
	 * @used-by cv()
	 * @param string|null $key [optional]
	 * @param bool $required [optional]
	 * @return mixed|null
	 */
	private function c($key = null, $required = true) {return
		dfc($this, function($key, $required = true) {
			/** @var mixed|null $result */
			$result = dfa($this->configCached(), $key);
			if ($required) {
				static::assertKeyIsDefined($key, $result);
			}
			return $result;
		}, [$key ?: df_caller_f(), $required])
	;}

	/**
	 * 2016-07-12
	 * @return void
	 */
	private function capture() {
		/** @var IOP|OP $payment */
		$payment = $this->ii();
		/** @var Method $method */
		$method = $payment->getMethodInstance();
		$method->setStore($this->o()->getStoreId());
		DfPayment::processActionS($payment, M::ACTION_AUTHORIZE_CAPTURE, $this->o());
		DfPayment::updateOrderS(
			$payment
			, $this->o()
			, Order::STATE_PROCESSING
			, $this->o()->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
			, $isCustomerNotified = true
		);
	}

	/**
	 * 2016-08-27
	 * @used-by c()
	 * @return array(string => mixed)
	 */
	private function configCached() {return dfc($this, function() {return $this->config() + [
		self::$readableStatusKey => dfa($this->config(), self::$statusKey)
	];});}

	/**
	 * 2016-07-11
	 * @used-by ii()
	 * @used-by parentIdG()
	 * @param string $localId
	 * @return string
	 * @uses \Df\Payment\Method::transactionIdL2G()
	 */
	private function idL2G($localId) {return dfp_method_call_s($this, 'transactionIdL2G', $localId);}

	/**
	 * 2016-07-10
	 * @return IOP|OP
	 */
	private function ii() {return dfc($this, function() {
		/** @var IOP|OP $result */
		$result = dfp_by_trans($this->tParent());
		dfp_trans_id($result, $this->id());
		return $result;
	});}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see validate().
	 * Отныне же @see validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by handle()
	 * @return bool
	 */
	private function isSuccessful() {return dfc($this, function() {return
		strval($this->statusExpected()) === strval($this->cv(self::$statusKey))
	;});}

	/**
	 * 2016-12-26
	 * @used-by handle()
	 * @return void
	 */
	private function log() {static::logStatic(
		$this->getData(), $this->typeLabel(), strval($this->cvo(self::$readableStatusKey))
	);}

	/**
	 * 2016-08-14
	 * @return Method
	 */
	private function m() {return dfc($this, function() {return
		df_ar($this->ii()->getMethodInstance(), Method::class)
	;});}

	/**
	 * 2016-07-10
	 * @return Order|DfOrder
	 */
	private function o() {return dfc($this, function() {
		/** @var Order|DfOrder $result */
		$result = $this->tParent()->getOrder();
		/**
		 * 2016-03-26
		 * Иначе будет создан новый объект payment.
		 * @used-by \Magento\Sales\Model\Order::getPayment()
		 */
		$result[IO::PAYMENT] = $this->ii();
		return $result;
	});}

	/**
	 * 2016-07-10
	 * @used-by tParent()
	 * @return string
	 */
	private function parentIdG() {return dfc($this, function() {return
		$this->idL2G($this->parentId())
	;});}

	/**
	 * 2016-08-17
	 * 2016-07-15
	 * Send email confirmation to the customer.
	 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/6
	 * It is implemented by analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Paypal/Model/Ipn.php#L312-L321
	 *
	 * 2016-07-15
	 * What is the difference between InvoiceSender and OrderSender?
	 * https://mage2.pro/t/1872
	 *
	 * 2016-07-18
	 * Раньше тут был код:
			$payment = $this->o()->getPayment();
			if ($payment && $payment->getCreatedInvoice()) {
				df_order_send_email($this->o());
			}
	 *
	 * 2016-08-17
	 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/13
	 * В сценарии оффлайновой оплаты мы попадаем в эту точку программы дважды:
	 * 1) Когда платёжная система уведомляет нас о том,
	 * что покупатель выбрал оффлайновый способ оплаты.
	 * В этом случае счёта ещё нет ($this->capture() выше не выполнялось),
	 * и отсылаем покупателю письмо с заказом.
	 *
	 * 2) Когда платёжная система уведомляет нас о приходе оплаты.
	 * В этом случае счёт уже присутствует, и отсылаем покупателю письмо со счётом.
	 *
	 * @used-by handle()
	 * @return void
	 */
	private function sendEmailIfNeeded() {
		/**
		 * 2016-08-17
		 * @uses \Magento\Sales\Model\Order::getEmailSent() говорит,
		 * было ли уже отослано письмо о заказе.
		 * Отсылать его повторно не надо.
		 */
		if (!$this->o()->getEmailSent()) {
			df_order_send_email($this->o());
		}
		/**
		 * 2016-08-17
		 * Помещаем код ниже в блок else,
		 * потому что если письмо с заказом уже отослано,
		 * то письмо со счётом отсылать не надо,
		 * даже если счёт присутствует и письмо о нём не отсылалось.
		 */
		else {
			/**
			 * 2016-08-17
			 * Перед вызовом
			 * @uses \Magento\Framework\Data\Collection::getLastItem()
			 * @var \Magento\Sales\Model\Order\Invoice $invoice
			 */
			/** @var \Magento\Sales\Model\Order\Invoice $invoice */
			$invoice = $this->o()->getInvoiceCollection()->getLastItem();
			/**
			 * 2016-08-17
			 * @uses \Magento\Framework\Data\Collection::getLastItem()
			 * возвращает объект, если коллекция пуста.
			 */
			if ($invoice->getId() && !$invoice->getEmailSent()) {
				df_invoice_send_email($invoice);
			}
		}
	}

	/**
	 * 2016-07-12
	 * @used-by ic()
	 * @param string|null $case [optional]
	 * @return array(string => string)
	 */
	private function testData($case = null) {
		/** @var string $classSuffix */
		$classSuffix = df_class_last($this);
		/**
		 * 2016-08-28
		 * Если у класса Response нет подклассов,
		 * то не используем суффикс Response в именах файлах тестовых данных,
		 * а случай confirm делаем случаем по умолчанию.
		 * /dfe-allpay/confirm/?class=BankCard => AllPay/BankCard.json
		 * /dfe-allpay/confirm/?class=BankCard&case=failure => AllPay/BankCard-failure.json
		 * /dfe-securepay/confirm/?dfTest=1 => SecurePay/confirm.json
		 */
		if ($classSuffix === df_class_last(__CLASS__)) {
			$classSuffix = null;
			$case = $case ?: $this->defaultTestCase();
		}
		/** @var string $basename */
		$basename = df_ccc('-', $classSuffix, $case);
		/** @var string $module */
		$module = df_module_name_short($this);
		/** @var string $file */
		$file = BP . df_path_n_real("/_my/test/{$module}/{$basename}.json");
		if (!file_exists($file)) {
			df_error("Please place the webhook's test data to the «%s» file.", $file);
		}
		return df_json_decode(file_get_contents($file));
	}

	/**
	 * 2016-07-10
	 * 2016-12-30
	 * Возвращает транзакцию Magento, породившую данное оповещение от платёжной системы (webhook event).
	 * В то же время не каждое оповещение от платёжной системы инициируется запросом от Magento:
	 * например, оповещение могло быть инициировано некими действиями администратора магазина
	 * в административном интерфейсе магазина в платёжной системе.
	 * Однако первичная транзакция всё равно должна в Magento присутствовать.
	 * @return T
	 */
	private function tParent() {return dfc($this, function() {return
		df_load(T::class, $this->parentIdG(), true, 'txn_id')
	;});}

	/**
	 * 2016-07-09
	 * http://php.net/manual/en/function.get-called-class.php#115790
	 * @param array(string => mixed)|bool $params
	 * @return self
	 */
	public static function i($params) {return self::ic(static::class, $params);}

	/**
	 * 2016-07-12
	 * @param string $class
	 * @param array(string => mixed)|string $params
	 * @return self
	 */
	public static function ic($class, $params) {
		/** @var self $result */
		$result = df_create($class);
		if (isset($params[self::$dfTest])) {
			unset($params[self::$dfTest]);
			/** @var string|null $case */
			$case = dfa($params, 'case');
			unset($params['case']);
			$params += $result->testData($case);
		}
		$result->setData($params);
		return $result;
	}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @used-by \Df\Payment\Webhook::execute()
	 * @param \Exception $e
	 * @return Text
	 */
	public static function resultError(\Exception $e) {
		static::logStatic($_REQUEST, $e);
		return Text::i(df_lets($e))->setHttpResponseCode(500);
	}

	/**
	 * 2016-08-28
	 * @used-by validate()
	 * @used-by \Dfe\AllPay\Webhook::i()
	 * @var string
	 */
	protected static $dfTest = 'dfTest';

	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Название ключа в сообщении от платёжной системы,
	 * содержащего идентификатор платежа в платёжной системе.
	 * @used-by externalId()
	 * @var string
	 */
	protected static $externalIdKey = 'externalIdKey';

	/**
	 * 2016-08-27
	 * @var string
	 */
	protected static $needCapture = 'needCapture';

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @var string
	 */
	protected static $readableStatusKey = 'readableStatusKey';

	/**
	 * 2016-08-27
	 * @var string
	 */
	protected static $statusExpected = 'statusExpected';

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @var string
	 */
	protected static $statusKey = 'statusKey';

	/**
	 * 2016-12-26
	 * @var string
	 */
	protected static $typeKey = 'typeKey';

	/**
	 * 2016-12-30
	 * @used-by c()
	 * @param string $key
	 * @param mixed $value
	 * @throws DFE
	 */
	private static function assertKeyIsDefined($key, $value) {
		if (is_null($value)) {
			df_error("The class %s should define a value for the parameter «%s».",
				static::class, $key
			);
		}
	}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @used-by resultError()
	 * @param array(string => string) $request
	 * @param \Exception|string $type
	 * @param string|null $status [optional]
	 * @return void
	 */
	private static function logStatic(array $request, $type, $status = null) {
		/** @var string $data */
		$data = df_json_encode_pretty($request);
		/** @var string $method */
		$code = dfp_method_code(static::class);
		/** @var string $title */
		$title = dfp_method_title(static::class);
		/** @var \Exception|string $v */
		/** @var string $suffix */
		list($v, $suffix) =
			$type instanceof \Exception
			? [$type, 'exception']
			: [df_ccc(': ', sprintf("[%s] {$type}", $title), $status), df_fs_name($type)]
		;
		df_sentry_m()->user_context(['id' => $title]);
		df_sentry($v, [
			'extra' => ['Payment Data' => $data, 'Payment Method' => $title]
			,'tags' => ['Payment Method' => $title]
		]);
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  '.log', $data);
	}
}
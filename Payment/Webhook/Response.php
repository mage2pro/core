<?php
namespace Df\Payment\Webhook;
use Df\Core\Exception as DFE;
use Df\Payment\Method;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Controller\AbstractResult as Result;
use Df\Framework\Controller\Result\Text;
use Magento\Payment\Model\Method\AbstractMethod as M;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Store\Model\Store;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Response extends \Df\Core\O {
	/**
	 * 2016-08-27
	 * @used-by configCached()
	 * @return array(string => mixed)
	 */
	abstract protected function config();

	/**
	 * 2016-07-10
	 * @used-by \Dfe\AllPay\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @used-by responseTransactionId()
	 * @return string
	 */
	public function externalId() {return $this->cv(self::$externalIdKey);}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	public function handle() {
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
				$this->order()->cancel();
			}
			else if ($this->needCapture()) {
				$this->capture();
			}
			$this->order()->save();
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
	 * 2016-07-10
	 * @return Order|DfOrder
	 */
	public function order() {
		if (!isset($this->_order)) {
			$this->_order = $this->tParent()->getOrder();
			/**
			 * 2016-03-26
			 * Иначе будет создан новый объект payment.
			 * @used-by \Magento\Sales\Model\Order::getPayment()
			 */
			$this->_order[IO::PAYMENT] = $this->payment();
		}
		return $this->_order;
	}

	/**
	 * 2016-07-09
	 * @used-by parentIdG()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @return string
	 */
	final public function parentId() {return $this[$this->requestIdKey()];}

	/**
	 * 2016-07-10
	 * @return IOP|OP
	 */
	public function payment() {return dfc($this, function() {
		/** @var IOP|OP $result */
		$result = dfp_by_trans($this->tParent());
		dfp_trans_id($result, $this->responseTransactionId());
		return $result;
	});}

	/**
	 * 2016-07-10
	 * @used-by \Dfe\SecurePay\Signer\Response::keys()
	 * @param string|null $key [optional]
	 * @return array(string => string)|string|null
	 */
	public function requestP($key = null) {
		$result = dfc($this, function() {
			/** @var array(string => string) $result */
			$result = $this->requestInfo();
			unset($result[Method::TRANSACTION_PARAM__URL]);
			return $result;
		});
		return is_null($key) ? $result : dfa($result, $key);
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function requestUrl() {return dfa($this->requestInfo(), Method::TRANSACTION_PARAM__URL);}

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
	protected function addTransaction() {
		/**
		 * 2016-08-29
		 * Идентификатор транзакции мы предварительно установили в методе
		 * @see payment()
		 */
		$this->m()->applyCustomTransId();
		dfp_set_transaction_info($this->payment(), $this->getData());
		/**
		 * 2016-07-12
		 * @used-by \Magento\Sales\Model\Order\Payment\Transaction\Builder::linkWithParentTransaction()
		 */
		$this->payment()->setParentTransactionId($this->tParent()->getTxnId());
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственная транзакции без специального назначения,
		 * и поэтому мы можем безопасно его использовать.
		 */
		$this->payment()->addTransaction(T::TYPE_PAYMENT);
	}

	/**
	 * 2016-08-27
	 * @param string|null $key [optional]
	 * @param string|null $d [optional]
	 * @param bool $required [optional]
	 * @return mixed
	 */
	protected function cv($key = null, $d = null, $required = true) {
		$key = $this->c($key ?: df_caller_f(), $required);
		return !$key || !$this->offsetExists($key) ? $d : $this[$key];
	}

	/**
	 * 2016-12-30
	 * @used-by \Df\Payment\Webhook\Response::log()
	 * @param string|null $key [optional]
	 * @param string|null $d [optional]
	 * @return mixed
	 */
	protected function cvo($key = null, $d = null) {return $this->cv($key ?: df_caller_f(), $d, false);}

	/**
	 * 2016-12-30
	 * @used-by testData()
	 * @see \Df\StripeClone\Response::defaultTestCase()
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
	 * @used-by handle()
	 * @return bool
	 */
	protected function needCapture() {return $this->c();}

	/**
	 * 2016-08-29
	 * Потомки перекрывают этот метод, когда ключ идентификатора запроса в запросе
	 * не совпадает с ключем идентификатора запроса в ответе.
	 * Так, в частности, происходит в модуле SecurePay:
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Response::requestIdKey()
	 *
	 * @uses \Df\Payment\R\ICharge::requestIdKey()
	 * @used-by requestId()
	 * @return string
	 */
	protected function requestIdKey() {return df_con_s($this, 'Charge', 'requestIdKey');}

	/**
	 * 2016-07-20
	 * @used-by payment()
	 * @return string
	 */
	protected function responseTransactionId() {return $this->idL2G($this->externalId());}

	/**
	 * 2016-08-27
	 * @used-by handle()
	 * @return Result
	 */
	protected function resultSuccess() {return Text::i('success');}

	/**
	 * 2016-12-25
	 * @return S
	 */
	protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @return string|int
	 */
	protected function statusExpected() {return $this->c();}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	protected function store() {return $this->order()->getStore();}

	/**
	 * 2016-12-26
	 * @used-by log()
	 * @return string
	 */
	final protected function type() {return $this->cv(self::$typeKey, 'confirmation');}

	/**
	 * 2016-12-26
	 * @used-by log()
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
	private function c($key = null, $required = true) {return dfc($this, function($key, $required = true) {
		/** @var mixed|null $result */
		$result = dfa($this->configCached(), $key);
		if ($required) {
			static::assertKeyIsDefined($key, $result);
		}
		return $result;
	}, [$key ?: df_caller_f(), $required]);}

	/**
	 * 2016-07-12
	 * @return void
	 */
	private function capture() {
		/** @var IOP|OP $payment */
		$payment = $this->payment();
		/** @var Method $method */
		$method = $payment->getMethodInstance();
		$method->setStore($this->order()->getStoreId());
		DfPayment::processActionS($payment, M::ACTION_AUTHORIZE_CAPTURE, $this->order());
		DfPayment::updateOrderS(
			$payment
			, $this->order()
			, Order::STATE_PROCESSING
			, $this->order()->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)
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
	 * @used-by payment()
	 * @used-by parentIdG()
	 * @param string $localId
	 * @return string
	 * @uses \Df\Payment\Method::transactionIdL2G()
	 */
	private function idL2G($localId) {return dfp_method_call_s($this, 'transactionIdL2G', $localId);}

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
		df_ar($this->payment()->getMethodInstance(), Method::class)
	;});}

	/**
	 * 2016-07-10
	 * @used-by tParent()
	 * @return string
	 */
	private function parentIdG() {return dfc($this, function() {return
		$this->idL2G($this->parentId())
	;});}

	/**
	 * 2016-07-10
	 * @return array(string => mixed)
	 */
	private function requestInfo() {return dfc($this, function() {return
		df_trans_raw_details($this->tParent())
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
			$payment = $this->order()->getPayment();
			if ($payment && $payment->getCreatedInvoice()) {
				df_order_send_email($this->order());
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
		if (!$this->order()->getEmailSent()) {
			df_order_send_email($this->order());
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
			$invoice = $this->order()->getInvoiceCollection()->getLastItem();
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
	 * 2016-07-12
	 * @used-by order()
	 * @var Order|DfOrder|null
	 */
	private $_order;

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
			unset($params[$params[self::$dfTest]]);
			/** @var string|null $case */
			$case = dfa($params, 'case');
			unset($params['case']);
			$params += $result->testData($case);
		}
		$result->setData($params);
		return $result;
	}

	/**
	 * 2016-08-28
	 * @used-by validate()
	 * @used-by \Dfe\AllPay\Response::i()
	 * @var string
	 */
	protected static $dfTest = 'dfTest';

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
	 * 2016-08-27
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
		df_sentry_m()->user_context([
			'id' => df_is_localhost() ? "$title webhook on localhost" : df_request_ua()
		]);
		df_sentry($v, [
			'extra' => ['Payment Data' => $data, 'Payment Method' => $title]
			,'tags' => ['Payment Method' => $title]
		]);
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  '.log', $data);
	}
}
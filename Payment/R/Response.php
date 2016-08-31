<?php
namespace Df\Payment\R;
use Df\Sales\Api\Data\TransactionInterface;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Controller\AbstractResult as Result;
use Df\Framework\Controller\Result\Text;
use Magento\Payment\Model\Method\AbstractMethod as M;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Store\Model\Store;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Response extends \Df\Core\O {
	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::configCached()
	 * @return array(string => mixed)
	 */
	abstract protected function config();

	/**
	 * 2016-07-10
	 * @used-by \Dfe\AllPay\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @used-by \Df\Payment\R\Response::responseTransactionId()
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
			$this->log();
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
				$this->sendEmail();
			}
			$result = $this->resultSuccess();
			df_log('OK');
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
			df_log('FAILURE');
			df_log($e);
		}
		return $result;
	}

	/**
	 * 2016-07-10
	 * @return Order|DfOrder
	 */
	public function order() {
		if (!isset($this->_order)) {
			$this->_order = $this->requestTransaction()->getOrder();
			/**
			 * 2016-03-26
			 * Very Important! If not done the order will create a duplicate payment
			 * @used-by \Magento\Sales\Model\Order::getPayment()
			 */
			$this->_order[IO::PAYMENT] = $this->payment();
		}
		return $this->_order;
	}

	/**
	 * 2016-07-10
	 * @return IOP|OP
	 */
	public function payment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfp_by_trans($this->requestTransaction());
			dfp_trans_id($this->{__METHOD__}, $this->responseTransactionId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return Report
	 */
	public function report() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Report::ic($this->reportC(), $this);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-09
	 * @used-by \Df\Payment\R\Response::requestIdG()
	 * @used-by \Dfe\AllPay\Block\Info::_prepareSpecificInformation()
	 * @return string
	 */
	public function requestId() {return $this->cv($this->requestIdKey());}

	/**
	 * 2016-08-29
	 * Потомки перекрывают этот метод, когда ключ идентификатора запроса в запросе
	 * не совпадает с ключем идентификатора запроса в ответе.
	 * Так, в частности, происходит в модуле SecurePay:
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Response::requestIdKey()
	 *
	 * @uses \Df\Payment\R\ICharge::requestIdKey()
	 * @used-by \Df\Payment\R\Response::requestId()
	 * @return string
	 */
	protected function requestIdKey() {return df_con_s($this, 'Charge', 'requestIdKey');}

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Report::asArray()
	 * @used-by \Dfe\SecurePay\Signer\Response::keys()
	 * @param string|null $key [optional]
	 * @return array(string => string)|string|null
	 */
	public function requestP($key = null) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->requestInfo();
			unset($this->{__METHOD__}[Method::TRANSACTION_PARAM__URL]);
		}
		return is_null($key) ? $this->{__METHOD__} : dfa($this->{__METHOD__}, $key);
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function requestUrl() {return dfa($this->requestInfo(), Method::TRANSACTION_PARAM__URL);}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see \Df\Payment\R\Response::isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() вернёт true.
	 * @see \Df\Payment\R\Response::isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @return bool|void
	 * @throws \Exception
	 */
	public function validate() {return $this->validateSignature();}

	/**
	 * 2016-07-12
	 * @return void
	 */
	protected function addTransaction() {
		/**
		 * 2016-08-29
		 * Идентификатор транзакции мы предварительно установили в методе
		 * @see \Df\Payment\R\Response::payment()
		 */
		$this->method()->applyCustomTransId();
		dfp_set_transaction_info($this->payment(), $this->getData());
		/**
		 * 2016-07-12
		 * @used-by \Magento\Sales\Model\Order\Payment\Transaction\Builder::linkWithParentTransaction()
		 */
		$this->payment()->setParentTransactionId($this->requestTransaction()->getTxnId());
		/**
		 * 2016-07-10
		 * @uses TransactionInterface::TYPE_PAYMENT — это единственный транзакции
		 * без специального назначения, и поэтому мы можем безопасно его использовать.
		 */
		$this->payment()->addTransaction(TransactionInterface::TYPE_PAYMENT);
	}

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Response::throwException()
	 * @return string
	 */
	protected function exceptionC() {return df_con_same_folder($this, 'Exception', Exception::class);}

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return void
	 */
	protected function handleBefore() {}

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return bool
	 */
	protected function needCapture() {return $this->c();}

	/**
	 * 2016-07-12
	 * @used-by \Df\Payment\R\Response::report()
	 * @return string
	 */
	protected function reportC() {return df_con_same_folder($this, 'Report', Report::class);}

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::payment()
	 * @return string
	 */
	protected function responseTransactionId() {return $this->idL2G($this->externalId());}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return Result
	 */
	protected function resultSuccess() {return Text::i('success');}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::isSuccessful()
	 * @return string|int
	 */
	protected function statusExpected() {return $this->c();}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	protected function store() {return $this->order()->getStore();}

	/**
	 * 2016-07-10
	 * @param Exception|string $message
	 * @return void
	 * @throws Exception
	 */
	protected function throwException($message) {
		/** @var Exception $exception */
		if ($message instanceof Exception) {
			$exception = $message;
		}
		else {
			/** @var string $exceptionClass */
			$exceptionClass = $this->exceptionC();
			/** @var Exception $exception */
			$exception = new $exceptionClass(df_format(func_get_args()), $this);
		}
		df_error($exception);
	}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::cv()
	 * @param string|null $key [optional]
	 * @return mixed
	 */
	private function c($key = null) {
		$key = $key ?: df_caller_f();
		if (!isset($this->{__METHOD__}[$key])) {
			/** @var mixed|null $result */
			$result = dfa($this->configCached(), $key);
			if (is_null($result)) {
				df_error("The class %s should define a value for the parameter «{$key}».", get_class($this));
			}
			$this->{__METHOD__}[$key] = $result;
		}
		return $this->{__METHOD__}[$key];
	}

	/**
	 * 2016-08-27
	 * @param string|null $key [optional]
	 * @return mixed
	 */
	private function cv($key = null) {return $this[$this->c($key ?: df_caller_f())];}

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
	 * @used-by \Df\Payment\R\Response::c()
	 * @return array(string => mixed)
	 */
	private function configCached() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->config();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-11
	 * @used-by \Df\Payment\R\Response::payment()
	 * @used-by \Df\Payment\R\Response::requestIdG()
	 * @param string $localId
	 * @return string
	 */
	private function idL2G($localId) {
		/** @uses \Df\Payment\Method::transactionIdL2G() */
		return dfp_method_call_s($this, 'transactionIdL2G', $localId);
	}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see \Df\Payment\R\Response::validate().
	 * Отныне же @see \Df\Payment\R\Response::validate() проверяет,
	 * корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see \Df\Payment\R\Response::validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return bool
	 */
	private function isSuccessful() {if (!isset($this->{__METHOD__})) {$this->{__METHOD__} =
		strval($this->statusExpected()) === strval($this->cv(self::$statusKey));
	}return $this->{__METHOD__};}

	/**
	 * 2016-07-06
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return void
	 */
	private function log() {
		/** @var string $code */
		$code = dfp_method_code($this);
		df_report("mage2.pro/{$code}-{date}--{time}.log", df_json_encode_pretty($this->getData()));
	}

	/**
	 * 2016-08-14
	 * @return Method
	 */
	private function method() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->payment()->getMethodInstance();
			df_assert_is(Method::class, $this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Response::requestTransaction()
	 * @return string
	 */
	private function requestIdG() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->idL2G($this->requestId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return array(string => mixed)
	 */
	private function requestInfo() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_trans_raw_details($this->requestTransaction());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return Transaction
	 */
	private function requestTransaction() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_load(Transaction::class, $this->requestIdG(), true, 'txn_id');
		}
		return $this->{__METHOD__};
	}

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
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return void
	 */
	private function sendEmail() {
		/**
		 * 2016-08-17
		 * @uses \Magento\Sales\Model\Order::getEmailSent() говорит, было ли уже отослано письмо о заказе.
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
	 * 2016-07-10
	 * @return string
	 */
	private function signatureProvided() {return $this->cv(self::$signatureKey);}

	/**
	 * 2016-07-12
	 * @used-by \Df\Payment\R\Response::ic()
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
			$case = $case ?: 'confirm';
		}
		/** @var string $basename */
		$basename = df_ccc('-', $classSuffix, $case);
		/** @var string $module */
		$module = df_module_name_short($this);
		return df_json_decode(file_get_contents(BP . "/_my/test/{$module}/{$basename}.json"));
	}

	/**
	 * 2016-07-09
	 * @used-by \Df\Payment\R\Response::validate()
	 * @return bool
	 * @throws \Exception
	 */
	private function validateSignature() {
		/** @var string $expected */
		$expected = Signer::signResponse($this, $this->getData());
		/** @var string $provided */
		$provided = $this->signatureProvided();
		/** @var bool $result */
		$result = $expected === $provided;
		if (!$result) {
			$this->throwException(
				"Invalid signature.\nExpected: «%s».\nProvided: «%s».", $expected, $provided
			);
		}
		return $result;
	}

	/**
	 * 2016-07-12
	 * @var Order|DfOrder
	 */
	protected $_order;

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
	 * @used-by \Df\Payment\R\Response::validate()
	 * @used-by \Dfe\AllPay\Response::i()
	 * @var string
	 */
	protected static $dfTest = 'dfTest';

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::handle()
	 * @used-by \Df\Payment\R\Confirm::execute()
	 * @param \Exception $e
	 * @return Text
	 */
	public static function resultError(\Exception $e) {
		return Text::i(df_lets($e))->setHttpResponseCode(500);
	}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::externalId()
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
	 * @used-by \Df\Payment\R\Response::signatureProvided()
	 * @var string
	 */
	protected static $signatureKey = 'signatureKey';

	/**
	 * 2016-08-27
	 * @var string
	 */
	protected static $statusExpected = 'statusExpected';

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Response::isSuccessful()
	 * @var string
	 */
	protected static $statusKey = 'statusKey';
}
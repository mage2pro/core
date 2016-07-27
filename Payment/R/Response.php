<?php
namespace Df\Payment\R;
use Df\Payment\Method;
use Df\Sales\Api\Data\TransactionInterface;
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Magento\Framework\Controller\AbstractResult as Result;
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
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод isSuccessful() вызывался из метода @see \Df\Payment\R\Response::validate().
	 * Отныне же @see \Df\Payment\R\Response::validate() проверяет,
	 * корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see \Df\Payment\R\Response::validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @return bool
	 */
	abstract public function isSuccessful();

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Response::externalId()
	 * @return string
	 */
	abstract protected function externalIdKey();

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return bool
	 */
	abstract protected function needCapture();

	/**
	 * 2016-07-09
	 * @used-by \Df\Payment\R\Response::requestId()
	 * @return string
	 */
	abstract protected function requestIdKey();

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @param \Exception $e
	 * @return Result
	 */
	abstract protected function resultError(\Exception $e);

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return Result
	 */
	abstract protected function resultSuccess();

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Response::signatureProvided()
	 * @return string
	 */
	abstract protected function signatureKey();

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function externalId() {return $this[$this->externalIdKey()];}

	/**
	 * 2016-07-04
	 * @override
	 * @return Result
	 */
	public function handle() {
		/** @var Result $result */
		try {
			$this->handleBefore();
			$this->log($this->getData());
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
			 * 2016-07-15
			 * Send email confirmation to the customer.
			 * https://code.dmitry-fedyuk.com/m2e/allpay/issues/6
			 * It is implemented by analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Paypal/Model/Ipn.php#L312-L321
			 */
			/**
			 * 2016-07-15
			 * What is the difference between InvoiceSender and OrderSender?
			 * https://mage2.pro/t/1872
			 */
			/**
			 * 2016-07-18
			 * Раньше тут был код:
					$payment = $this->order()->getPayment();
					if ($payment && $payment->getCreatedInvoice()) {
						df_order_send_email($this->order());
					}
			 */
			df_order_send_email($this->order());
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
			$result = $this->resultError($e);
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
			$this->{__METHOD__} = df_order_payment_get($this->requestTransaction()->getPaymentId());
			$this->{__METHOD__}[Method::CUSTOM_TRANS_ID] = $this->responseTransactionId();
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
	public function requestId() {return $this[$this->requestIdKey()];}

	/**
	 * 2016-07-10
	 * @used-by \Df\Payment\R\Report::asArray()
	 * @return array(string => mixed)
	 */
	public function requestParams() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->requestInfo();
			unset($this->{__METHOD__}[Method::TRANSACTION_PARAM__URL]);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	public function requestUrl() {return dfa($this->requestInfo(), Method::TRANSACTION_PARAM__URL);}

	/**
	 * 2016-07-14
	 * У этого метода значение по умолчанию аргумента $throw
	 * отличается от значения по умолчанию одноимённого аргумента
	 * метода @uses \Df\Payment\R\Response::validate()
	 * @param bool $throw[optional]
	 * @see \Df\Payment\R\Response::isSuccessful()
	 * @return bool
	 */
	public function validAndSuccessful($throw = false) {
		return $this->validate($throw) && $this->isSuccessful();
	}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see \Df\Payment\R\Response::isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() вернёт true.
	 * @see \Df\Payment\R\Response::isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @param bool $throw[optional]
	 * @return bool|void
	 * @throws \Exception
	 */
	public function validate($throw = true) {return $this->validateSignature($throw);}

	/**
	 * 2016-07-12
	 * @return void
	 */
	protected function addTransaction() {
		df_payment_apply_custom_transaction_id($this->payment());
		df_payment_set_transaction_info($this->payment(), $this->getData());
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
	protected function exceptionC() {return df_convention_same_folder($this, 'Exception', Exception::class);}

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::handle()
	 * @return void
	 */
	protected function handleBefore() {}

	/**
	 * 2016-07-12
	 * @used-by \Df\Payment\R\Response::report()
	 * @return string
	 */
	protected function reportC() {return df_convention_same_folder($this, 'Report', Report::class);}

	/**
	 * 2016-07-20
	 * @used-by \Df\Payment\R\Response::payment()
	 * @return string
	 */
	protected function responseTransactionId() {return $this->idL2G($this->externalId());}

	/**
	 * 2016-07-19
	 * @return Store
	 */
	protected function store() {return $this->order()->getStore();}

	/**
	 * 2016-07-12
	 * @param string $type
	 * @return array(string => string)
	 */
	protected function testData($type) {return [];}

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
	 * 2016-07-11
	 * @used-by \Df\Payment\R\Response::payment()
	 * @used-by \Df\Payment\R\Response::requestIdG()
	 * @param string $localId
	 * @return string
	 */
	private function idL2G($localId) {
		/** @uses \Df\Payment\Method::transactionIdL2G() */
		return call_user_func([$this->methodC(), 'transactionIdL2G'], $localId);
	}

	/**
	 * 2016-07-06
	 * @param mixed $message
	 * @return void
	 */
	private function log($message) {if (!df_my_local()) {df_log($message);}}

	/**
	 * 2016-07-10
	 * @return string
	 */
	private function methodC() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Method');
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
	 * 2016-07-10
	 * @return Signer
	 */
	private function signer() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_create(df_convention($this, 'Signer'), $this->getData());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	private function signatureProvided() {return $this[$this->signatureKey()];}

	/**
	 * 2016-07-09
	 * @used-by \Df\Payment\R\Response::validate()
	 * @param bool $throw[optional]
	 * @return bool
	 * @throws \Exception
	 */
	private function validateSignature($throw = true) {
		/** @var string $expected */
		$expected = $this->signer()->sign();
		/** @var string $provided */
		$provided = $this->signatureProvided();
		/** @var bool $result */
		$result = $expected === $provided;
		if (!$result && $throw) {
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
		/** @var bool $isSimulation */
		$isSimulation = isset($params['class']);
		if ($isSimulation) {
			unset($params['class']);
			/** @var string|null $case */
			$case = dfa($params, 'case');
			unset($params['case']);
			$params += $result->testData($case);
		}
		$result->setData($params);
		return $result;
	}
}
<?php
// 2016-12-26
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
use Df\Payment\Exception;
use Df\Payment\Source\ACR;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/** @method Settings s($key = '', $scope = null, $default = null) */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-12-28
	 * Информация о банковской карте.
	 * «How is the \Magento\Sales\Model\Order\Payment's setCcLast4() / getCcLast4() used?»
	 * https://mage2.pro/t/941
	 * @used-by chargeNew()
	 * @see \Dfe\Omise\Method::apiCardInfo()
	 * @see \Dfe\Stripe\Method::apiCardInfo()
	 * @param object $charge
	 * @return array(string => string)
	 */
	abstract protected function apiCardInfo($charge);

	/**
	 * 2016-12-28
	 * @used-by charge()
	 * @see \Dfe\Omise\Method::apiChargeCapturePreauthorized()
	 * @see \Dfe\Stripe\Method::apiChargeCapturePreauthorized()
	 * @param string $chargeId
	 * @return object
	 */
	abstract protected function apiChargeCapturePreauthorized($chargeId);

	/**
	 * 2016-12-28
	 * @used-by chargeNew()
	 * @see \Dfe\Omise\Method::apiChargeCreate()
	 * @see \Dfe\Stripe\Method::apiChargeCreate()
	 * @param array(string => mixed) $params
	 * @return object
	 */
	abstract protected function apiChargeCreate(array $params);

	/**
	 * 2016-12-28
	 * @used-by chargeNew()
	 * @param object $charge
	 * @return string
	 */
	abstract protected function apiChargeId($charge);

	/**
	 * 2016-12-27
	 * @used-by transInfo()
	 * @param object $response
	 * @return array(string => mixed)
	 */
	abstract protected function responseToArray($response);

	/**
	 * 2016-12-26
	 * @used-by transUrl()
	 * @param T $t
	 * @return string
	 */
	abstract protected function transUrlBase(T $t);

	/**
	 * 2016-11-13
	 * @override
	 * @see \Df\Payment\Method::canCapture()
	 * @return bool
	 */
	final public function canCapture() {return true;}

	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Payment\Method::canRefund()
	 * @return bool
	 */
	final public function canRefund() {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::canReviewPayment()
	 * @return bool
	 */
	final public function canReviewPayment() {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::canVoid()
	 * @return bool
	 */
	final public function canVoid() {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::denyPayment()
	 * @param II|I|OP  $payment
	 * @return bool
	 */
	final public function denyPayment(II $payment) {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::initialize()
	 * @param string $paymentAction
	 * @param object $stateObject
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L336-L346
	 * @see \Magento\Sales\Model\Order::isPaymentReview()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L821-L832
	 * @return void
	 */
	final public function initialize($paymentAction, $stateObject) {
		$stateObject['state'] = O::STATE_PAYMENT_REVIEW;
	}

	/**
	 * 2016-11-13
	 * @override
	 * @see \Df\Payment\Method::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L2336-L346
	 * 2016-12-24
	 * Сценарий «Review» не применяется при включенности проверки 3D Secure.
	 * @return bool
	 */
	final public function isInitializeNeeded() {return
		ACR::REVIEW === $this->getConfigPaymentAction()
	;}

	/**
	 * 2017-01-12
	 * Этот метод, в отличие от @see _3dsNeed(),
	 * принимает решение о необходимости проверки 3D Secure
	 * на основании конкретного параметра $charge.
	 * @used-by chargeNew()
	 * @see \Dfe\Omise\Method::_3dsNeedForCharge()
	 * @param object $charge
	 * @return bool
	 */
	protected function _3dsNeedForCharge($charge) {return false;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::_void()
	 * @return void
	 */
	final protected function _void() {$this->_refund(null);}

	/**
	 * 2016-03-07
	 * @override
	 * @see https://stripe.com/docs/charges
	 * @see \Df\Payment\Method::charge()
	 * @param float $amount
	 * @param bool|null $capture [optional]
	 * @return void
	 * @throws \Stripe\Error\Card
	 */
	final protected function charge($amount, $capture = true) {
		df_sentry_extra('Amount', $amount);
		df_sentry_extra('Need Capture?', df_bts($capture));
		/** @var T|false|null $auth */
		$auth = !$capture ? null : $this->ii()->getAuthorizationTransaction();
		if (!$auth) {
			$this->chargeNew($amount, $capture);
		}
		else {
			/** @var string $txnId */
			$txnId = $auth->getTxnId();
			df_sentry_extra('Parent Transaction ID', $txnId);
			/** @var string $chargeId */
			$chargeId = self::i2e($txnId);
			df_sentry_extra('Charge ID', $chargeId);
			$this->transInfo($this->apiChargeCapturePreauthorized($chargeId));
			/**
			 * 2016-12-16
			 * Система в этом сценарии по-умолчанию формирует идентификатор транзации как
			 * «<идентификатор родительской транзации>-capture».
			 * У нас же идентификатор родительской транзации имеет окончание «<-authorize»,
			 * и оно нам реально нужно (смотрите комментарий к ветке else ниже),
			 * поэтому здесь мы окончание «<-authorize» вручную подменяем на «-capture».
			 */
			$this->ii()->setTransactionId(self::e2i($chargeId, self::T_CAPTURE));
		}
	}

	/**
	 * 2016-12-28
	 * @used-by charge()
	 * @used-by \Dfe\Omise\Method::_3dsUrl()
	 * @param float $amount
	 * @param bool $capture
	 * @return object
	 */
	final protected function chargeNew($amount, $capture) {return dfc($this, function($amount, $capture) {
		/** @uses \Df\StripeClone\Charge::request() */
		/** @var array(string => mixed) $params */
		$params = df_con_s($this, 'Charge', 'request', [$this, $this->token(), $amount, $capture]);
		df_sentry_extra('Request Params', $params);
		/** @var object $result */
		$result = $this->apiChargeCreate($params);
		$this->iiaAdd($this->apiCardInfo($result));
		$this->transInfo($result, $params);
		/** @var bool $need3DS */
		$need3DS = $this->_3dsNeedForCharge($result);
		/**
		 * 2016-03-15
		 * Иначе операция «void» (отмена авторизации платежа) будет недоступна:
		 * «How is a payment authorization voiding implemented?»
		 * https://mage2.pro/t/938
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 *
		 * 2016-12-16
		 * Раньше мы окончание не добавляли, и это приводило к проблеме https://mage2.pro/t/2381
		 * При Refund из интерфейса Stripe метод \Dfe\Stripe\Handler\Charge\Refunded::process()
		 * находил транзакцию типа «capture» путём добавления окончания «-capture»
		 * к идентификатору платежа в Stripe.
		 * Однако если у платежа не было стадии «authorize»,
		 * то в данной точке кода окончание «capture» не добавлялось,
		 * а вот поэтому Refund из интерфейса Stripe не работал.
		 */
		$this->ii()->setTransactionId(self::e2i(
			$this->apiChargeId($result)
			,$need3DS ? self::T_3DS : ($capture ? self::T_CAPTURE : self::T_AUTHORIZE)
		));
		/**
		 * 2016-03-15
		 * Аналогично, иначе операция «void» (отмена авторизации платежа) будет недоступна:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 * Транзакция ситается завершённой, если явно не указать «false».
		 */
		$this->ii()->setIsTransactionClosed(!$need3DS && $capture);
		if ($need3DS) {
			/**
			 * 2016-07-10
			 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
			 * это единственный транзакция без специального назначения,
			 * и поэтому мы можем безопасно его использовать
			 * для сохранения информации о нашем запросе к платёжной системе.
			 * 2017-01-12
			 * Сделал по аналогии с @see \Df\PaypalClone\Method::addTransaction()
			 * Иначе транзакция не будет записана.
			 * Что интересно, если првоерка 3D Secure не нужна,
			 * то и этой специальной операции записи транзакции не нужно:
			 * она будет записана автоматически.
			 */
			$this->ii()->addTransaction(T::TYPE_PAYMENT);
		}
		return $result;
	}, func_get_args());}

	/**
	 * 2016-05-03
	 * @override
	 * @see \Df\Payment\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @return string[]
	 */
	final protected function iiaKeys() {return [self::$TOKEN];}

	/**
	 * 2016-12-27
	 * @used-by \Dfe\Omise\Method::_charge()
	 * @used-by \Dfe\Stripe\Method::charge()
	 * @return string
	 */
	final protected function token() {return $this->iia(self::$TOKEN);}

	/**
	 * 2016-12-27
	 * @used-by chargeNew()
	 * @used-by \Dfe\Omise\Method::_refund()
	 * @used-by \Dfe\Omise\Method::charge()
	 * @used-by \Dfe\Stripe\Method::_refund()
	 * @used-by \Dfe\Stripe\Method::charge()
	 * @param object $response
	 * @param array(string => mixed) $request [optional]
	 * @return void
	 */
	final protected function transInfo($response, array $request = []) {
		/** @var array(string => mixed) $responseA */
		$responseA = $this->responseToArray($response);
		if ($this->s()->log()) {
			// 2017-01-12
			// В локальный лог попадает только response, а в Sentry: и request, и response.
			dfp_report($this, $responseA, df_caller_ff());
		}
		$this->iiaSetTRR(array_map('df_json_encode_pretty', [$request, $responseA]));
	}

	/**
	 * 2016-08-20
	 * @override
	 * Хотя Stripe использует для страниц транзакций адреса вида
	 * https://dashboard.stripe.com/test/payments/<id>
	 * адрес без части «test» также успешно работает (даже в тестовом режиме).
	 * Использую именно такие адреса, потому что я не знаю,
	 * какова часть вместо «test» в промышленном режиме.
	 * @see \Df\Payment\Method::transUrl()
	 * @used-by \Df\Payment\Method::formatTransactionId()
	 * @param T $t
	 * @return string
	 */
	final protected function transUrl(T $t) {return df_cc_path(
		$this->transUrlBase($t), self::i2e($t->getTxnId())
	);}

	/**
	 * 2016-12-16
	 * 2017-01-05
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by charge()
	 * @used-by chargeNew()
	 * @used-by \Dfe\Omise\Method::_refund()
	 * @used-by \Dfe\Stripe\Method::_refund()
	 * @used-by \Df\StripeClone\Method::e2i()
	 * @used-by \Df\StripeClone\Webhook::e2i()
	 * @param string $id
	 * @param string $txnType
	 * @return string
	 */
	final public static function e2i($id, $txnType) {
		df_param_string_not_empty($id, 0);
		return self::i2e($id) . "-$txnType";
	}

	/**
	 * 2017-01-12
	 * @used-by chargeNew()
	 * @used-by \Dfe\Omise\Webhook\Charge\Complete::parentTransactionType()
	 */
	const T_3DS = '3ds';
	/**
	 * 2017-01-12
	 * @used-by chargeNew()
	 * @used-by \Dfe\Stripe\Webhook\Charge\Captured::parentTransactionType()
	 */
	const T_AUTHORIZE = 'authorize';
	/**
	 * 2017-01-12
	 * @used-by charge()
	 * @used-by chargeNew()
	 * @used-by \Dfe\Omise\Webhook\Charge\Complete::currentTransactionType()
	 * @used-by \Dfe\Stripe\Webhook\Charge\Captured::currentTransactionType()
	 * @used-by \Dfe\Stripe\Webhook\Charge\Refunded::parentTransactionType()
	 */
	const T_CAPTURE = 'capture';
	/**
	 * 2017-01-12
	 * @used-by \Dfe\Omise\Method::_refund()
	 * @used-by \Dfe\Stripe\Method::_refund()
	 * @used-by \Dfe\Stripe\Webhook\Charge\Refunded::currentTransactionType()
	 */
	const T_REFUND = 'refund';

	/**
	 * 2016-08-20
	 * 2017-01-05
	 * Преобразует внутренний идентификатор транзакции во внешний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by charge()
	 * @used-by e2i()
	 * @used-by transUrl()
	 * @used-by \Dfe\Stripe\Method::_refund()
	 * @used-by \Dfe\Omise\Method::_refund()
	 * @used-by \Dfe\Stripe\Method::transUrl()
	 * @param string $id
	 * @return string
	 */
	final protected static function i2e($id) {
		df_param_string_not_empty($id, 0);
		/** @var string $result */
		$result = df_first(explode('-', $id));
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * 2016-03-06
	 * 2016-08-23
	 * Отныне для Stripe этот параметр может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * @var string
	 */
	private static $TOKEN = 'token';
}
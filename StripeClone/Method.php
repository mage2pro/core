<?php
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
use Df\Payment\Source\ACR;
use Df\Payment\Token;
use Df\StripeClone\Facade\Charge as fCharge;
use Df\StripeClone\Facade\O as fO;
use Df\StripeClone\Facade\Preorder as fPreorder;
use Df\StripeClone\Facade\Refund as fRefund;
use Df\StripeClone\P\Charge as pCharge;
use Df\StripeClone\P\Preorder as pPreorder;
use Df\StripeClone\W\Event as Ev;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Creditmemo as CM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-12-26
 * @see \Dfe\Iyzico\Method
 * @see \Dfe\Moip\Method
 * @see \Dfe\Omise\Method
 * @see \Dfe\Paymill\Method
 * @see \Dfe\Spryng\Method
 * @see \Dfe\Square\Method
 * @see \Dfe\Stripe\Method
 * @method Settings s($k = null, $s = null, $d = null)
 */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-12-26
	 * @used-by transUrl()
	 * @see \Dfe\Iyzico\Method::transUrlBase()
	 * @see \Dfe\Moip\Method::transUrlBase()
	 * @see \Dfe\Omise\Method::transUrlBase()
	 * @see \Dfe\Paymill\Method::transUrlBase()
	 * @see \Dfe\Spryng\Method::transUrlBase()
	 * @see \Dfe\Square\Method::transUrlBase()
	 * @see \Dfe\Stripe\Method::transUrlBase()
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
	final function canCapture() {return true;}

	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Payment\Method::canRefund()
	 * @return bool
	 */
	final function canRefund() {return true;}

	/**
	 * 2016-03-08
	 * @override
	 * @see \Df\Payment\Method::canRefundPartialPerInvoice()
	 * @return bool
	 */
	final function canRefundPartialPerInvoice() {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::canReviewPayment()
	 * @return bool
	 */
	final function canReviewPayment() {return true;}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::canVoid()
	 * @return bool
	 */
	final function canVoid() {return true;}

	/**
	 * 2016-03-07
	 * @override
	 * @see https://stripe.com/docs/charges
	 * @see \Df\Payment\Method::charge()
	 * @used-by \Df\Payment\Method::authorize()
	 * @used-by \Df\Payment\Method::capture()
	 * @used-by \Dfe\Omise\Init\Action::redirectUrl()
	 * @param bool|null $capture [optional]
	 * @throws \Stripe\Error\Card
	 */
	final function charge($capture = true) {
		df_sentry_extra($this, 'Amount', $a = dfp_due($this)); /** @var float $a */
		df_sentry_extra($this, 'Need Capture?', df_bts($capture));
		/** @var T|false|null $auth */
		if (!($auth = !$capture ? null : $this->ii()->getAuthorizationTransaction())) {
			$this->chargeNew($capture);
		}
		else {
			/** @var string $txnId */
			df_sentry_extra($this, 'Parent Transaction ID', $txnId = $auth->getTxnId());
			df_sentry_extra($this, 'Charge ID', $id = $this->i2e($txnId)); /** @var string $id */
			$this->transInfo($this->fCharge()->capturePreauthorized($id, $this->amountFormat($a)));
			// 2016-12-16
			// Система в этом сценарии по-умолчанию формирует идентификатор транзации как
			// «<идентификатор родительской транзации>-capture».
			// У нас же идентификатор родительской транзации имеет окончание «<-authorize»,
			// и оно нам реально нужно (смотрите комментарий к ветке else ниже),
			// поэтому здесь мы окончание «<-authorize» вручную подменяем на «-capture».
			$this->ii()->setTransactionId($this->e2i($id, Ev::T_CAPTURE));
		}
	}

	/**
	 * 2016-03-15
	 * @override
	 * @see \Df\Payment\Method::denyPayment()
	 * @param II|I|OP  $payment
	 * @return bool
	 */
	final function denyPayment(II $payment) {return true;}

	/**
	 * 2016-03-15
	 * 2017-03-25
	 * У нас не получилось бы установить заказу состояние @uses ACR::R
	 * непосредственно в @see getConfigPaymentAction(),
	 * потому что @used-by \Magento\Sales\Model\Order\Payment::place() устанавливает заказу
	 * состояние @see \Magento\Sales\Model\Order::STATE_NEW в том случае,
	 * когда getConfigPaymentAction() возвращает null.
	 * Поэтому для установки состояния ACR::R мы вынуждены действовать чуть сложнее.
	 * @override
	 * @see \Df\Payment\Method::initialize()
	 * @param string $action
	 * @param object $dto
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L336-L346
	 * @see \Magento\Sales\Model\Order::isPaymentReview()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L821-L832
	 */
	final function initialize($action, $dto) {$dto['state'] = O::STATE_PAYMENT_REVIEW;}

	/**
	 * 2017-07-30
	 * Whether the current payment is by a bank card.
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @see \Dfe\Moip\Method::isCard()
	 * @return bool
	 */
	function isCard() {return true;}

	/**
	 * 2016-11-13
	 * @override
	 * @see \Df\Payment\Method::isInitializeNeeded()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L336-L346
	 * 2016-12-24 Сценарий «Review» не применяется при включенности проверки 3D Secure.
	 * 2017-03-25
	 * У нас не получилось бы установить заказу состояние @uses ACR::R
	 * непосредственно в @see getConfigPaymentAction(),
	 * потому что @used-by \Magento\Sales\Model\Order\Payment::place() устанавливает заказу
	 * состояние @see \Magento\Sales\Model\Order::STATE_NEW в том случае,
	 * когда getConfigPaymentAction() возвращает null.
	 * Поэтому для установки состояния ACR::R мы вынуждены действовать чуть сложнее.
	 * @return bool
	 */
	final function isInitializeNeeded() {return ACR::R === $this->getConfigPaymentAction();}

	/**
	 * 2017-01-19
	 * @override
	 * @see \Df\Payment\Method::_refund()
	 * @used-by \Df\Payment\Method::refund()
	 * @param float|null $a
	 */
	final protected function _refund($a) {
		$ii = $this->ii(); /** @var OP $ii */
		/**
		 * 2016-03-17
		 * Метод @uses \Magento\Sales\Model\Order\Payment::getAuthorizationTransaction()
		 * необязательно возвращает транзакцию типа «авторизация»:
		 * в возвращает родительскую (предыдущую) транзакцию:
		 * @see \Magento\Sales\Model\Order\Payment\Transaction\Manager::getAuthorizationTransaction()
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment/Transaction/Manager.php#L31-L47
		 * Это как раз то, что нам нужно, ведь наш модуль может быть настроен сразу на capture,
		 * без предварительной транзакции типа «авторизация».
		 */
		if ($tPrev = $ii->getAuthorizationTransaction() /** @var T|false $tPrev */) {
			$id = $this->i2e($tPrev->getTxnId() /** @var string $id */);
			// 2016-03-24
			// Credit Memo и Invoice отсутствуют в сценарии Authorize / Capture
			// и присутствуют в сценарии Capture / Refund.
			$cm = $ii->getCreditmemo(); /** @var CM|null $cm */
			$fc = $this->fCharge(); /** @var fCharge $fc */
			$resp = $cm ? $fc->refund($id, $this->amountFormat($a)) : $fc->void($id); /** @var object $resp */
			$this->transInfo($resp);
			$ii->setTransactionId($this->e2i($id, $cm ? Ev::T_REFUND : 'void'));
			if ($cm) {
				/**
				 * 2017-01-19
				 * Записаваем идентификатор операции в БД,
				 * чтобы затем, при обработке оповещений от платёжной системы,
				 * проверять, не было ли это оповещение инициировано нашей же операцией,
				 * и если было, то не обрабатывать его повторно:
				 * @see \Df\Payment\W\Strategy\Refund::_handle()
				 * https://github.com/mage2pro/core/blob/1.12.16/StripeClone/WebhookStrategy/Charge/Refunded.php?ts=4#L21-L23
				 */
				dfp_container_add($this->ii(), self::II_TRANS, fRefund::s($this)->transId($resp));
			}
		}
	}

	/**
	 * 2017-02-10
	 * @used-by charge()
	 * @used-by chargeNew()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @return fCharge
	 */
	final protected function fCharge() {return fCharge::s($this);}

	/**
	 * 2016-05-03
	 * @override
	 * @see \Df\Payment\Method::iiaKeys()
	 * @used-by \Df\Payment\Method::assignData()
	 * @see \Dfe\Moip\Method::iiaKeys()
	 * @see \Dfe\Square\Method::iiaKeys()
	 * @see \Dfe\Stripe\Method::iiaKeys()
	 * @return string[]
	 */
	protected function iiaKeys() {return [Token::KEY];}

	/**
	 * 2017-02-01
	 * Отныне @see \Df\Payment\Method::action() логирую только на своих серверах.
	 * Аналогично поступаю и с игнорируемыми webhooks:
	 * @see \Df\Payment\W\Action::notImplemented()
	 * @override
	 * @see \Df\Payment\Method::needLogActions()
	 * @used-by \Df\Payment\Method::action()
	 * @return bool
	 */
	final protected function needLogActions() {return df_my();}

	/**
	 * 2017-01-12
	 * Этот метод, в отличие от @see \Df\Payment\Init\Action::redirectUrl(),
	 * принимает решение о необходимости перенаправления
	 * (пока — только проверки 3D Secure, но возможны и другие варианты,
	 * т.к. Stripe вроде бы стал поддерживать Bancontact и другие европейские платёжные системы).
	 * на основании конкретного параметра $charge.
	 * @used-by chargeNew()
	 * @see \Dfe\Omise\Method::redirectNeeded()
	 * @param object|array(string => mixed) $c
	 * @return bool
	 */
	protected function redirectNeeded($c) {return false;}

	/**
	 * 2017-07-30
	 * 2017-08-02 For now, it is never overriden.
	 * @used-by chargeNew()
	 * @return string
	 */
	final protected function transPrefixForRedirectCase() {return Ev::T_3DS;}

	/**
	 * 2016-08-20
	 * @override
	 * Хотя Stripe использует для страниц транзакций адреса вида
	 * https://dashboard.stripe.com/test/payments/<id>
	 * адрес без части «test» также успешно работает (даже в тестовом режиме).
	 * Использую именно такие адреса, потому что я не знаю,
	 * какова часть вместо «test» в промышленном режиме.
	 * 2017-02-19
	 * Метод возвращает null в том случае, когда у платежа нет URL в инретфейсе платёжной системы.
	 * Так, к сожалению, у Spryng:
	 * [Spryng] It would be nice to have an unique URL
	 * for each transaction inside the merchant interface: https://mage2.pro/t/2847
	 * @see \Df\Payment\Method::transUrl()
	 * @used-by \Df\Payment\Method::tidFormat()
	 * @param T $t
	 * @return string|null                                                 
	 */
	final protected function transUrl(T $t) {return !($b = $this->transUrlBase($t)) ? null :
		"$b/{$this->i2e($t->getTxnId())}"
	;}

	/**
	 * 2016-12-28
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by charge()
	 * @param bool $capture
	 * @return object|array(string => mixed)
	 */
	private function chargeNew($capture) {return dfc($this, function($capture) {
		$fc = $this->fCharge(); /** @var fCharge $fc */
		/**
		 * 2017-06-11
		 * Some PSPs like Moip require 2 steps to make a payment:
		 * 1) Creating an «order».
		 * 2) Creating a «payment».
		 * To implement such a scheme, the @uses \Df\StripeClone\P\Charge::request()
		 * should return data for the both requests,
		 * and then @uses \Df\StripeClone\Facade\Charge::create() should make the both requests.
		 */
		/** @var array(string => mixed) $preorderParams */
		if ($needPreorder = $fc->needPreorder() /** @var bool $needPreorder */) {
			$preorderParams = pPreorder::request($this);
			df_sentry_extra($this, 'Preorder Params', $preorderParams);
			$fc->preorderSet(fPreorder::s($this)->create($preorderParams));
		}
		$p = pCharge::request($this, $capture); /** @var array(string => mixed) $p */
		df_sentry_extra($this, 'Request Params', $p);
		$result = $fc->create($p); /** @var object|array(string => mixed) $result */
		if ($this->isCard()) {
			/**
			 * 2017-07-24
			 * Unfortunately, the one-liner fails on PHP 5.6.30:
			 * $this->iiaAdd((CardFormatter::s($this, $fc->card($result)))->ii());
			 * «syntax error, unexpected '->' (T_OBJECT_OPERATOR)»
			 * https://github.com/mage2pro/core/issues/15
			 * See also: https://github.com/mage2pro/core/issues/16
			 *
			 * 2017-07-31
			 * Interestingly, the following code works on all PHP versions >= 5.4.0:
			 * class A {function f() {return __METHOD__;}
			 * echo (new A)->f();
			 * https://3v4l.org/nANqg
			 *
			 * The following code works on all PHP versions >= 5.0.0:
			 *	class A {
			 *		function f() {return __METHOD__;}
			 *		static function s() {return new self;}
			 *	}
			 *	echo A::s()->f();
			 * https://3v4l.org/1qr48
			 *
			 * The same is true for the following code:
			 *	class A {
			 *		function f() {return $this->_v;}
			 *		final protected function __construct($v) {$this->_v = $v;}
			 *		private $_v;
			 *		final static function s($v) {return new self($v);}
			 *	}
			 *	echo A::s('test')->f();
			 * https://3v4l.org/2E8H6
			 *
			 * So, I was unable to reproduce the issue...
			 * Maybe the problem customer uses a non-standard (patched) PHP version?
			 *
			 * 2017-11-08
			 * Note 1.
			 * One-liners like (!($e = $a->e())->b()) are really not supported by PHP < 7:
			 * https://3v4l.org/lJjvS
			 * Note 2.
			 * At the same time, some `))->` one-liners are supported by PHP >= 5.4, e.g:
			 * static function p() {return (new self())->b();}
			 * https://3v4l.org/LJlDE
			 * Note 3.   
			 * If we add extra brackets to the code from the Note 2,
			 * then the code will be incompatible with PHP < 7:
			 * static function p() {return ((new self()))->b();}
			 */
			$cf = CardFormatter::s($this, $fc->card($result)); /** @var CardFormatter $cf */
			$this->iiaAdd($cf->ii());
		}
		$this->transInfo($result, $p + (!$needPreorder ? [] : ['df_preorder' => $preorderParams]));
		$needRedirect = $this->redirectNeeded($result); /** @var bool $needRedirect */
		$i = $this->ii(); /** @var II|OP|QP $i */
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
		$i->setTransactionId($this->e2i($fc->id($result),
			$needRedirect ? $this->transPrefixForRedirectCase() :
				($capture ? Ev::T_CAPTURE : Ev::T_AUTHORIZE)
		));
		/**
		 * 2016-03-15
		 * Если оставить открытой транзакцию «capture»,
		 * то операция «void» (отмена авторизации платежа) будет недоступна:
		 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order/Payment.php#L540-L555
		 * @used-by \Magento\Sales\Model\Order\Payment::canVoid()
		 * Транзакция считается закрытой, если явно не указать «false».
		 *
		 * 2017-01-16
		 * Наоборот: если закрыть транзакцию типа «authorize»,
		 * то операция «Capture Online» из административного интерфейса будет недоступна:
		 * @see \Magento\Sales\Model\Order\Payment::canCapture()
		 *		if ($authTransaction && $authTransaction->getIsClosed()) {
		 *			$orderTransaction = $this->transactionRepository->getByTransactionType(
		 *				Transaction::TYPE_ORDER,
		 *				$this->getId(),
		 *				$this->getOrder()->getId()
		 *			);
		 *			if (!$orderTransaction) {
		 *				return false;
		 *			}
		 *		}
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Model/Order/Payment.php#L263-L281
		 * «How is \Magento\Sales\Model\Order\Payment::canCapture() implemented and used?»
		 * https://mage2.pro/t/650
		 * «How does Magento 2 decide whether to show the «Capture Online» dropdown
		 * on a backend's invoice screen?»: https://mage2.pro/t/2475
		 */
		$i->setIsTransactionClosed($capture && !$needRedirect);
		if ($needRedirect) {
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
			$i->addTransaction(T::TYPE_PAYMENT);
		}
		return $result;
	}, func_get_args());}

	/**
	 * 2016-12-16
	 * 2017-01-05
	 * The method translates an external (from a PSP) payment identifier to an internal one.
	 * Usually it is done by adding the `-<transaction type>` suffix.
	 * @used-by _refund()
	 * @used-by charge()
	 * @used-by chargeNew()
	 * @param string $id
	 * @param string $type
	 * @return string
	 */
	private function e2i($id, $type) {return $this->tid()->e2i($id, $type);}

	/**
	 * 2016-08-20
	 * 2017-01-05
	 * Преобразует внутренний идентификатор транзакции во внешний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by _refund()
	 * @used-by charge()
	 * @used-by e2i()
	 * @used-by transUrl()
	 * @param string $id
	 * @return string
	 */
	private function i2e($id) {return $this->tid()->i2e($id);}

	/**
	 * 2016-12-27
	 * @used-by _refund()
	 * @used-by charge()
	 * @used-by chargeNew()
	 * @param object $response
	 * @param array(string => mixed) $request [optional]
	 */
	private function transInfo($response, array $request = []) {
		$responseA = fO::s($this)->toArray($response); /** @var array(string => mixed) $responseA */
		if ($this->s()->log()) {
			// 2017-01-12 В локальный лог попадает только response, а в Sentry: и request, и response.
			dfp_report($this, $responseA, df_caller_ff());
		}
		$this->iiaSetTRR($request, $responseA);
	}
}
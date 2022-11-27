<?php
namespace Df\Payment\W;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Df\Payment\W\Exception\Critical;
/**
 * 2017-03-09
 * @see \Df\PaypalClone\W\Event
 * @see \Df\StripeClone\W\Event
 */
abstract class Event implements IEvent, IMA {
	/**
	 * 2017-01-16
	 * @used-by self::pid()
	 * @see \Df\GingerPaymentsBase\W\Event::k_pid()
	 * @see \Df\StripeClone\W\Event::k_pid()
	 * @see \Dfe\AllPay\W\Event::k_pid()
	 * @see \Dfe\AlphaCommerceHub\W\Event::k_pid()
	 * @see \Dfe\Dragonpay\W\Event::k_pid()
	 * @see \Dfe\IPay88\W\Event::k_pid()
	 * @see \Dfe\PostFinance\W\Event::k_pid()
	 * @see \Dfe\Qiwi\W\Event::k_pid()
	 * @see \Dfe\Robokassa\W\Event::k_pid()
	 * @see \Dfe\SecurePay\W\Event::k_pid()
	 * @see \Dfe\YandexKassa\W\Event::k_pid()
	 */
	abstract protected function k_pid():string;

	/**
	 * 2017-01-06
	 * 2017-03-18 The type of the current transaction.
	 * 2017-08-30
	 * 1) If you want to ignore an event in @see \Df\Payment\W\Strategy\ConfirmPending::_handle(), then:
	 * 		1) Return `true` from @see \Df\Payment\W\Event::isSuccessful()
	 * 		2) Return any value except \Df\Payment\W\Event::T_AUTHORIZE and \Df\Payment\W\Event::T_CAPTURE
	 * 		from @see \Df\Payment\W\Event::ttCurrent().
	 * 2) This value will be the current transaction suffix:
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @used-by \Df\StripeClone\W\Nav::id()
	 * so it should be unique in a payment processing cycle:
	 * a particular payment can not have multiple transactions with the same suffix.
	 * 2022-11-10 The result could be an empty string: @see \Dfe\Moip\W\Event::ttCurrent()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @used-by \Df\StripeClone\W\Nav::id()
	 * @see \Df\GingerPaymentsBase\W\Event::ttCurrent()
	 * @see \Df\PaypalClone\W\Event::ttCurrent()
	 * @see \Dfe\Moip\W\Event::ttCurrent()
	 * @see \Dfe\Omise\W\Event\Charge\Capture::ttCurrent()
	 * @see \Dfe\Omise\W\Event\Charge\Complete::ttCurrent()
	 * @see \Dfe\Omise\W\Event\Refund::ttCurrent()
	 * @see \Dfe\Paymill\W\Event\Refund::ttCurrent()
	 * @see \Dfe\Paymill\W\Event\Transaction\Succeeded::ttCurrent()
	 * @see \Dfe\Stripe\W\Event\Charge\Captured::ttCurrent()
	 * @see \Dfe\Stripe\W\Event\Charge\Refunded::ttCurrent()
	 * @see \Dfe\TBCBank\W\Event::ttCurrent()
	 */
	abstract function ttCurrent():string;

	/**
	 * 2017-03-10
	 * @used-by \Df\Payment\W\F::event()
	 */
	final function __construct(Reader $r) {$this->_r = $r;}

	/**
	 * 2017-11-08
	 * @used-by \Df\Payment\W\Action::execute()
	 * @see \Dfe\Stripe\W\Event\Source::checkIgnored()
	 * @return false|string
	 */
	function checkIgnored() {return false;}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see validate().
	 * Отныне же @see validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 *
	 * 2017-08-30
	 * If you want to ignore an event in @see \Df\Payment\W\Strategy\ConfirmPending::_handle(), then:
	 * 1) Return `true` from @see \Df\Payment\W\Event::isSuccessful()
	 * 2) Return any value except \Df\Payment\W\Event::T_AUTHORIZE and \Df\Payment\W\Event::T_CAPTURE
	 * from @see \Df\Payment\W\Event::ttCurrent().
	 * This value will be the current transaction suffix:
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @used-by \Df\StripeClone\W\Nav::id()
	 * so it should be unique in a payment processing cycle:
	 * a particular payment can not have multiple transactions with the same suffix.
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\PaypalClone\W\Event::ttCurrent()
	 * @see \Df\PaypalClone\W\Event::isSuccessful()
	 * @see \Dfe\Stripe\W\Event\Source::isSuccessful()
	 * @see \Dfe\TBCBank\W\Event::isSuccessful()
	 */
	function isSuccessful():bool {return true;}

	/**
	 * 2017-03-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @override
	 * @see \Df\Payment\IMA::m()
	 * @used-by \Df\Payment\W\Nav::mPartial()
	 * @used-by \Df\PaypalClone\Signer::_sign()
	 * @used-by \Df\PaypalClone\W\Exception\InvalidSignature::__construct()
	 * @used-by \Dfe\Stripe\W\Event\Source::ttCurrent()
	 * @used-by \Dfe\TBCBank\W\Event::ttCurrent()
	 */
	function m():M {return $this->_r->m();}

	/**
	 * 2016-07-09
	 * 2017-01-04
	 * 2017-03-16
	 * Возвращает некую основу для вычисления идентификатора родительской транзакции в Magento.
	 * Эта основа в настоящее время бывает 2-х видов:
	 * 1) Идентификатор платежа в платёжной системе.
	 * Так происходит для Stripe-подобных модулей.
	 * На основе этого идентификатора мы:
	 *     1.1) вычисляем идентификатор родительской транзакции
	 *     (посредством прибавления окончания «-<тип родительской транзакции>»)
	 *     1.2) создаём идентификатор текущей транзакции
	 *     (аналогично, посредством прибавления окончания «-<тип текущей транзакции>»).
	 * 2) Переданный нами ранее платёжной системе наш внутренний идентификатор родительской транзакции
	 * (т.е., запроса к платёжой системе) в локальном (коротком) формате
	 * (т.е. без приставки «<имя платёжного модуля>-»).
	 * @used-by \Df\Payment\W\Nav::pid()
	 * @used-by \Df\PaypalClone\W\Event::idE()
	 * @used-by \Df\StripeClone\W\Event::idBase()
	 * @used-by \Dfe\Robokassa\W\Responder::success()
	 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
	 * @see \Dfe\Qiwi\W\Event::pid()
	 */
	function pid():string {return $this->rr(df_assert_sne($this->k_pid()));}

	/**
	 * 2017-03-10
	 * @override
	 * @see \Df\Payment\W\IEvent::r()
	 * @used-by \Df\Payment\W\Exception::r()
	 * @used-by \Df\Payment\W\Handler::r()
	 * @used-by \Df\PaypalClone\W\Event::validate()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\W\Event::currencyName()
	 * @used-by \Dfe\AlphaCommerceHub\W\Event::ttCurrent()
	 * @used-by \Dfe\Dragonpay\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\W\Event::option()
	 * @used-by \Dfe\PostFinance\W\Event::cardholder()
	 * @used-by \Dfe\PostFinance\W\Event::cardNumber()
	 * @used-by \Dfe\PostFinance\W\Event::option()
	 * @used-by \Dfe\PostFinance\W\Event::optionTitle()
	 * @used-by \Dfe\Qiwi\W\Handler::amount()
	 * @used-by \Dfe\Robokassa\W\Event::optionTitle()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
	 * @used-by \Dfe\Stripe\W\Event\Source::isSuccessful()
	 * @used-by \Dfe\TBCBank\Block\Info::cardData()
	 * @used-by \Dfe\TBCBank\W\Event::_3dsStatus()
	 * @used-by \Dfe\TBCBank\W\Event::isSuccessful()
	 * @used-by \Dfe\TBCBank\W\Event::paymentStatus()
	 * @used-by \Dfe\TBCBank\W\Event::rrn()
	 * @used-by \Dfe\TBCBank\W\Strategy\ConfirmPending::onSuccess()
	 * @used-by \Dfe\YandexKassa\Result::attributes()
	 * @param string|string[] $k [optional]
	 * @param string|null $d [optional]
	 * @return string|int|mixed|null|array(string => mixed)
	 */
	final function r($k = '', $d = null) {return $this->_r->r($k, $d);}

	/**
	 * 2017-03-13
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\Payment\W\Action::execute()
	 */
	function rd():Reader {return $this->_r;}

	/**
	 * 2017-01-12
	 * @used-by self::pid()
	 * @used-by \Df\PaypalClone\W\Event::idE()
	 * @used-by \Df\PaypalClone\W\Event::signatureProvided()
	 * @used-by \Df\PaypalClone\W\Event::status()
	 * @used-by \Df\StripeClone\W\Event::ro()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed
	 * @throws Critical
	 */
	final function rr($k = null, $d = null) {return $this->_r->rr($k, $d);}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @see \Df\PaypalClone\W\Event::statusT()
	 * @see \Dfe\Stripe\W\Event\Source::statusT()
	 */
	function statusT():string {return '';}

	/**
	 * 2017-03-10
	 * 2017-03-13 Returns a value in our internal format, not in the PSP format.
	 * @used-by self::tl()
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @used-by \Df\Payment\W\Event::tl()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
	 * @used-by \Dfe\AllPay\W\Event\Offline::statusExpected()
	 * @used-by \Dfe\YandexKassa\Result::tag()
	 */
	final function t():string {return $this->_r->t();}

	/**
	 * 2017-03-10 Type label.
	 * @override
	 * @see \Df\Payment\W\IEvent::tl()
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @used-by \Df\Payment\W\Handler::log() 
	 * @used-by \Dfe\AllPay\Choice::title()
	 */
	final function tl():string {return dfc($this, function() {return $this->tl_(
		$this->useRawTypeForLabel() ? $this->_r->tRaw() : $this->t()
	);});}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see \Df\PaypalClone\W\Event::isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @see \Df\PaypalClone\W\Event::validate()
	 * @throws \Exception
	 */
	function validate():void {}

	/**
	 * 2017-03-13
	 * @used-by self::tl()
	 * @see \Dfe\AllPay\W\Event::useRawTypeForLabel()
	 */
	protected function useRawTypeForLabel():bool {return false;}

	/**
	 * 2017-03-13
	 * @used-by self::tl()
	 * @used-by \Df\Payment\W\Event::tl_()
	 * @see \Dfe\AllPay\W\Event::tl_()
	 */
	protected function tl_(string $t):string {return $this->_r->tl_($t);}

	/**
	 * 2017-03-10
	 * @used-by self::__construct()
	 * @used-by self::r()
	 * @used-by self::rd()
	 * @used-by self::t()
	 * @var Reader
	 */
	private $_r;

	/**
	 * 2017-01-12
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @used-by \Dfe\Omise\W\Event\Charge\Complete::ttParent()
	 * @used-by \Dfe\Stripe\Init\Action::transId()
	 * @used-by \Dfe\Stripe\W\Event\Source::ttParent()
	 */
	const T_3DS = '3ds';

	/**
	 * 2017-01-12
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @used-by \Dfe\AlphaCommerceHub\W\Event::ttCurrent()
	 * @used-by \Dfe\Omise\W\Event\Charge\Capture::ttParent()
	 * @used-by \Dfe\Paymill\W\Event\Transaction\Succeeded::ttParent()
	 * @used-by \Dfe\PostFinance\W\Event::ttCurrent()
	 * @used-by \Dfe\Stripe\W\Event\Charge\Captured::ttParent()
	 * @used-by \Dfe\TBCBank\W\Event::ttCurrent()
	 */
	const T_AUTHORIZE = 'authorize';

	/**
	 * 2017-01-12
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\PaypalClone\W\Event::ttCurrent()
	 * @used-by \Df\StripeClone\Method::charge()
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @used-by \Dfe\AllPay\W\Event\Offline::statusExpected()
	 * @used-by \Dfe\AllPay\W\Event\Offline::ttCurrent()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @used-by \Dfe\AlphaCommerceHub\W\Event::ttCurrent()
	 * @used-by \Dfe\Dragonpay\W\Event::ttCurrent()
	 * @used-by \Dfe\Omise\W\Event\Charge\Capture::ttCurrent()
	 * @used-by \Dfe\Omise\W\Event\Charge\Complete::ttCurrent()
	 * @used-by \Dfe\Omise\W\Event\Refund::ttParent()
	 * @used-by \Dfe\Paymill\W\Event\Refund::ttParent()
	 * @used-by \Dfe\PostFinance\W\Event::ttCurrent()
	 * @used-by \Dfe\Qiwi\W\Event::ttCurrent()
	 * @used-by \Dfe\Stripe\W\Event\Charge\Captured::ttCurrent()
	 * @used-by \Dfe\Stripe\W\Event\Charge\Refunded::ttParent()
	 * @used-by \Dfe\Stripe\W\Event\Source::ttCurrent()
	 * @used-by \Dfe\TBCBank\W\Event::ttCurrent()
	 * @used-by \Dfe\YandexKassa\W\Event::ttCurrent()
	 */
	const T_CAPTURE = 'capture';

	/**
	 * 2017-08-16
	 * @used-by \Df\PaypalClone\W\Event::ttCurrent()
	 * @used-by \Dfe\AllPay\W\Event\Offline::ttCurrent()
	 * @used-by \Dfe\Dragonpay\W\Event::ttCurrent()
	 * @used-by \Dfe\PostFinance\W\Event::ttCurrent()
	 * @used-by \Dfe\Qiwi\W\Event::ttCurrent()
	 * @used-by \Dfe\YandexKassa\W\Event::ttCurrent()
	 */
	const T_INFO = 'info';

	/**
	 * 2017-03-26
	 * Первичная транзакция.
	 * Она всегда соответствует неподтверждённому состоянию платежа.
	 * @used-by \Df\GingerPaymentsBase\Init\Action::transId()
	 * @used-by \Df\GingerPaymentsBase\W\Event::ttParent()
	 */
	const T_INIT = 'init';

	/**
	 * 2017-01-12
	 * @used-by \Df\StripeClone\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\Omise\W\Event\Refund::ttCurrent()
	 * @used-by \Dfe\Paymill\W\Event\Refund::ttCurrent()
	 * @used-by \Dfe\Paymill\W\Event\Transaction\Succeeded::ttCurrent()
	 * @used-by \Dfe\PostFinance\W\Event::ttCurrent()
	 * @used-by \Dfe\Qiwi\W\Event::ttCurrent()
	 * @used-by \Dfe\Qiwi\W\Handler::strategyC()
	 * @used-by \Dfe\Stripe\W\Event\Charge\Refunded::ttCurrent()
	 */
	const T_REFUND = 'refund';

	/**
	 * 2017-08-16
	 * 2017-08-18 @deprecated It is unused.
	 */
	const T_VOID = 'void';
}
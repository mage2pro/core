<?php
namespace Df\PaypalClone\W;
/**
 * 2017-03-16
 * @see \Dfe\AllPay\W\Event
 * @see \Dfe\IPay88\W\Event
 * @see \Dfe\SecurePay\W\Event
 */
abstract class Event extends \Df\Payment\W\Event {
	/**
	 * 2017-01-16
	 * @used-by idE()
	 * @see \Df\GingerPaymentsBase\W\Event::k_idE()
	 * @see \Dfe\AllPay\W\Event::k_idE()
	 * @see \Dfe\IPay88\W\Event::k_idE()
	 * @see \Dfe\SecurePay\W\Event::k_idE()
	 * @return string
	 */
	abstract protected function k_idE();

	/**
	 * 2017-01-18
	 * @used-by signatureProvided()
	 * @see \Df\GingerPaymentsBase\W\Event::k_signature()
	 * @see \Dfe\AllPay\W\Event::k_signature()
	 * @see \Dfe\SecurePay\W\Event::k_signature()
	 * @return string
	 */
	abstract protected function k_signature();

	/**
	 * 2017-01-18
	 * @used-by status()
	 * @see \Df\GingerPaymentsBase\W\Event::k_status()
	 * @see \Dfe\AllPay\W\Event::k_status()
	 * @see \Dfe\IPay88\W\Event::k_status()
	 * @see \Dfe\SecurePay\W\Event::k_status()
	 * @return string
	 */
	abstract protected function k_status();

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @see \Df\GingerPaymentsBase\W\Event::statusExpected()
	 * @see \Dfe\AllPay\W\Event::statusExpected()
	 * @see \Dfe\AllPay\W\Event\Offline::statusExpected()
	 * @see \Dfe\IPay88\W\Event::statusExpected()
	 * @see \Dfe\SecurePay\W\Event::statusExpected()
	 * @return string|int
	 */
	abstract protected function statusExpected();

	/**
	 * 2017-03-16
	 * Идентификатор платежа в платёжной системе.
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @return string
	 */
	final function idE() {return $this->rr($this->k_idE());}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see validate().
	 * Отныне же @see validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * 2017-01-06
	 * Кэшировать результат этого метода не нужно, потому что он вызывается лишь единократно:
	 * @used-by \Df\PaypalClone\W\Handler::_handle()
	 * @return bool
	 */
	final function isSuccessful() {return strval($this->statusExpected()) === strval($this->status());}

	/**
	 * 2016-07-20
	 * @used-by \Df\PaypalClone\W\Handler::_handle()
	 * @used-by \Dfe\AllPay\W\Event\Offline::statusExpected()
	 * @used-by \Dfe\AllPay\W\Nav\Offline::id()
	 * @see \Dfe\AllPay\W\Event\Offline::needCapture()
	 * @return bool
	 */
	function needCapture() {return true;}

	/**
	 * 2017-03-18
	 * @used-by \Df\PaypalClone\W\Handler::validate()
	 * @return string
	 */
	final function signatureProvided() {return $this->rr($this->k_signature());}

	/**
	 * 2017-01-18
	 * @used-by logTitleSuffix()
	 * @see \Dfe\IPay88\W\Event::k_statusT()
	 * @see \Dfe\SecurePay\W\Event::k_statusT()
	 * @return string|null
	 */
	protected function k_statusT() {return null;}

	/**
	 * 2017-01-02    
	 * @override
	 * @see \Df\Payment\W\Event::logTitleSuffix()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @return string|null
	 */
	final function logTitleSuffix() {return ($k = $this->k_statusT()) ? $this->r($k) : $this->status();}

	/**
	 * 2017-03-18
	 * @used-by isSuccessful()
	 * @used-by logTitleSuffix()
	 * @return string
	 */
	private function status() {return $this->rr($this->k_status());}
}
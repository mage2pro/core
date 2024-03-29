<?php
namespace Df\PaypalClone\W;
use Df\PaypalClone\Signer;
use Df\PaypalClone\W\Exception\InvalidSignature;
/**
 * 2017-03-16
 * @see \Dfe\AllPay\W\Event
 * @see \Dfe\AlphaCommerceHub\W\Event
 * @see \Dfe\Dragonpay\W\Event
 * @see \Dfe\IPay88\W\Event
 * @see \Dfe\PostFinance\W\Event
 * @see \Dfe\Qiwi\W\Event
 * @see \Dfe\Robokassa\W\Event
 * @see \Dfe\SecurePay\W\Event
 * @see \Dfe\YandexKassa\W\Event
 */
abstract class Event extends \Df\Payment\W\Event {
	/**
	 * 2017-01-16
	 * 2017-04-16, 2017-11-22
	 * Some PSPs (Robokassa, partially AlphaCommerceHub) do not return their own identifier for a payment.
	 * The k_idE() method for such PSPs can return an empty string,
	 * and then a presudo-identifier will be generated automatically.
	 * Such identifier serves only as `txn_id` for the current Magento transaction.
	 * @used-by self::idE()
	 * @see \Dfe\AllPay\W\Event::k_idE()
	 * @see \Dfe\AlphaCommerceHub\W\Event::k_idE()
	 * @see \Dfe\Dragonpay\W\Event::k_idE()
	 * @see \Dfe\IPay88\W\Event::k_idE()
	 * @see \Dfe\PostFinance\W\Event::k_idE()
	 * @see \Dfe\Qiwi\W\Event::k_idE()
	 * @see \Dfe\Robokassa\W\Event::k_idE()
	 * @see \Dfe\SecurePay\W\Event::k_idE()
	 * @see \Dfe\YandexKassa\W\Event::k_idE()
	 */
	abstract protected function k_idE():string;

	/**
	 * 2017-01-18
	 * 2022-11-27 The result could be an empty string.
	 * @used-by self::signatureProvided()
	 * @see \Dfe\AllPay\W\Event::k_signature()
	 * @see \Dfe\AlphaCommerceHub\W\Event::k_signature()
	 * @see \Dfe\Dragonpay\W\Event::k_signature()
	 * @see \Dfe\IPay88\W\Event::k_signature()
	 * @see \Dfe\PostFinance\W\Event::k_signature()
	 * @see \Dfe\Qiwi\W\Event::k_signature()
	 * @see \Dfe\Robokassa\W\Event::k_signature()
	 * @see \Dfe\SecurePay\W\Event::k_signature()
	 * @see \Dfe\YandexKassa\W\Event::k_signature()
	 */
	abstract protected function k_signature():string;

	/**
	 * 2017-01-18
	 * 2017-04-16 Некоторые ПС (Robokassa) не возвращают статуса. Для таких ПС метод должен возвращать ''.
	 * @used-by self::status()
	 * @see \Dfe\AllPay\W\Event::k_status()
	 * @see \Dfe\AlphaCommerceHub\W\Event::k_status()
	 * @see \Dfe\Dragonpay\W\Event::k_status()
	 * @see \Dfe\IPay88\W\Event::k_status()
	 * @see \Dfe\PostFinance\W\Event::k_status()
	 * @see \Dfe\Qiwi\W\Event::k_status()
	 * @see \Dfe\Robokassa\W\Event::k_status()
	 * @see \Dfe\SecurePay\W\Event::k_status()
	 * @see \Dfe\YandexKassa\W\Event::k_status()
	 */
	abstract protected function k_status():string;

	/**
	 * 2017-03-16 The payment's identifier in the PSP.
	 * 2017-04-16
	 * Некоторые ПС (Robokassa) не возвращают своего идентификатора для платежей
	 * (возвращают только идентификатор, заданный магазином).
	 * Для таких ПС формируем псевдо-идентификатор платежа в ПС самостоятельно.
	 * Он будет использован только для присвоения в качестве txn_id текущей транзакции.
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\PostFinance\Block\Info::prepare()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @see \Dfe\Qiwi\W\Event::idE()
	 */
	function idE():string {return dfa_strict($this->rr(), $this->k_idE(), "{$this->pid()}e");}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see self::validate().
	 * Отныне же @see self::validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see self::validate() вернёт true.
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
	 *
	 * @override
	 * @see \Df\Payment\W\Event::isSuccessful()
	 * @used-by self::ttCurrent()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\W\Event::ttCurrent()
	 * @see \Dfe\Dragonpay\W\Event::isSuccessful()
	 * @see \Dfe\PostFinance\W\Event::isSuccessful()
	 * @see \Dfe\Qiwi\W\Event::isSuccessful()
	 * @see \Dfe\YandexKassa\W\Event::isSuccessful()
	 */
	function isSuccessful():bool {return dfc($this, function() {return $this->statusExpected() === $this->status();});}

	/**
	 * 2017-01-02
	 * @override
	 * @see \Df\Payment\W\Event::statusT()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 */
	final function statusT():string {return dfa_strict($this->r(), $this->k_statusT(), function() {return dftr(
		$this->status(), df_module_json($this, 'statuses', false)
	);});}

	/**
	 * 2017-08-15 The type of the current transaction.
	 * 2016-07-10
	 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
	 * это единственная транзакции без специального назначения, и поэтому мы можем безопасно его использовать.
	 * 2017-08-30
	 * If you want to ignore an event in @see \Df\Payment\W\Strategy\ConfirmPending::_handle(), then:
	 * 1) Return `true` from isSuccessful()
	 * 2) Return any value except \Df\Payment\W\Event::T_AUTHORIZE and \Df\Payment\W\Event::T_CAPTURE
	 * from @see ttCurrent().
	 * This value will be the current transaction suffix: @used-by \Df\PaypalClone\W\Nav::id()
	 * so it should be unique in a payment processing cycle:
	 * a particular payment can not have multiple transactions with the same suffix.
	 * @override
	 * @see \Df\Payment\W\Event::ttCurrent()
	 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
	 * @used-by \Df\PaypalClone\W\Nav::id()
	 * @see \Dfe\AllPay\W\Event\Offline::ttCurrent()
	 * @see \Dfe\AlphaCommerceHub\W\Event::ttCurrent()
	 * @see \Dfe\Dragonpay\W\Event::ttCurrent()
	 * @see \Dfe\PostFinance\W\Event::ttCurrent()
	 * @see \Dfe\Qiwi\W\Event::ttCurrent()
	 * @see \Dfe\YandexKassa\W\Event::ttCurrent()
	 */
	function ttCurrent():string {return $this->isSuccessful() ? self::T_CAPTURE : self::T_INFO;}

	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * 2017-04-16 Сделал проверку независимой от высоты букв.
	 * @override
	 * @see \Df\Payment\W\Event::validate()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @see \Dfe\AlphaCommerceHub\W\Event::validate()
	 * @throws \Exception
	 */
	function validate():void {
		$e = Signer::signResponse($this, $this->r()); /** @var string $e */
		$p = $this->signatureProvided(); /** @var string $p */
		if (!df_strings_are_equal_ci($e, $p)) {
			df_error(new InvalidSignature($this, $e, $p));
		}
	}

	/**
	 * 2017-01-18
	 * @used-by self::statusT()
	 * @see \Dfe\AlphaCommerceHub\W\Event::k_statusT()
	 * @see \Dfe\Dragonpay\W\Event::k_statusT()
	 * @see \Dfe\IPay88\W\Event::k_statusT()
	 * @see \Dfe\SecurePay\W\Event::k_statusT()
	 */
	protected function k_statusT():string {return '';}

	/**
	 * 2017-03-18
	 * 2017-04-16 Некоторые ПС (Robokassa) не возвращают статуса. Для таких ПС метод должен возвращать null.
	 * @used-by self::isSuccessful()
	 * @used-by self::statusT()
	 * @used-by \Dfe\Dragonpay\W\Event::isSuccessful()
	 * @used-by \Dfe\Dragonpay\W\Event::ttCurrent()
	 * @used-by \Dfe\PostFinance\W\Event::isSuccessful()
	 * @used-by \Dfe\PostFinance\W\Event::s0()
	 * @used-by \Dfe\Qiwi\W\Event::isSuccessful()
	 * @used-by \Dfe\Qiwi\W\Event::ttCurrent()
	 * @used-by \Dfe\YandexKassa\W\Event::isSuccessful()
	 * @used-by \Dfe\YandexKassa\W\Event::ttCurrent()
	 */
	final protected function status():string {return strval(dfa_strict($this->rr(), $this->k_status(), ''));}

	/**
	 * 2016-08-27
	 * 2017-04-16 Некоторые ПС (Robokassa) не возвращают статуса. Для таких ПС метод должен возвращать ''.
	 * @used-by self::isSuccessful()
	 * @see \Dfe\AllPay\W\Event::statusExpected()
	 * @see \Dfe\AllPay\W\Event\Offline::statusExpected()
	 * @see \Dfe\AlphaCommerceHub\W\Event::statusExpected()
	 * @see \Dfe\IPay88\W\Event::statusExpected()
	 * @see \Dfe\SecurePay\W\Event::statusExpected()
	 */
	protected function statusExpected():string {return '';}

	/**
	 * 2017-03-18
	 * @used-by self::validate()
	 */
	private function signatureProvided():string {return dfa_strict($this->rr(), $this->k_signature(), '');}
}
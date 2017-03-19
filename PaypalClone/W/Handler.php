<?php
namespace Df\PaypalClone\W;
use Df\PaypalClone\Signer;
/**
 * 2016-07-09
 * @see \Df\PaypalClone\W\Confirmation
 * @method Event e()
 */
abstract class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @override
	 * @see \Df\Payment\W\Handler::validate()
	 * @used-by \Df\Payment\W\Handler::handle()
	 * @see \Df\GingerPaymentsBase\W\Handler::validate()
	 * @return void
	 * @throws \Exception
	 */
	protected function validate() {
		/** @var string $e */
		/** @var string $p */
		if (($e = Signer::signResponse($this, $this->r())) !== ($p = $this->e()->signatureProvided())) {
			df_error("Invalid signature.\nExpected: «{$e}».\nProvided: «{$p}».");
		}
	}
}
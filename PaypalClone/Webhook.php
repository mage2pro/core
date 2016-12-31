<?php
namespace Df\PaypalClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2016-07-09
	 * 2016-07-14
	 * Раньше метод @see isSuccessful() вызывался из метода validate().
	 * Отныне же validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то validate() не возбудит исключительной ситуации.
	 * @see isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @override
	 * @see \Df\Payment\Webhook::validate()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return void
	 * @throws \Exception
	 */
	public function validate() {
		/** @var string $expected */
		$expected = Signer::signResponse($this, $this->getData());
		/** @var string $provided */
		$provided = $this->signatureProvided();
		/** @var bool $result */
		$result = $expected === $provided;
		if (!$result) {
			df_error("Invalid signature.\nExpected: «{$expected}».\nProvided: «{$provided}».");
		}
	}

	/**
	 * 2016-07-10
	 * @return string
	 */
	private function signatureProvided() {return $this->cv(self::$signatureKey);}

	/**
	 * 2016-08-27
	 * @used-by signatureProvided()
	 * @var string
	 */
	protected static $signatureKey = 'signatureKey';
}
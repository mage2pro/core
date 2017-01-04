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
	final public function validate() {
		/** @var string $expected */
		$expected = Signer::signResponse($this, $this->req());
		/** @var string $provided */
		$provided = $this->cv(self::$signatureKey);
		/** @var bool $result */
		$result = $expected === $provided;
		if (!$result) {
			df_error("Invalid signature.\nExpected: «{$expected}».\nProvided: «{$provided}».");
		}
	}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see validate().
	 * Отныне же @see validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * @used-by \Df\PaypalClone\Confirmation::_handle()
	 * @return bool
	 */
	final protected function isSuccessful() {return dfc($this, function() {return
		strval($this->statusExpected()) === strval($this->status())
	;});}

	/**
	 * 2017-01-02
	 * @used-by log()
	 * @return string|null
	 */
	final protected function logTitleSuffix() {return
		$this->cvo(self::$readableStatusKey, $this->status())
	;}

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @used-by \Dfe\AllPay\Webhook\Offline::statusExpected()
	 * @see \Dfe\AllPay\Webhook\Offline::statusExpected()
	 * @return string|int
	 */
	protected function statusExpected() {return $this->c();}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::testDataFile()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function testDataFile() {
		/** @var string|null $case */
		$case = $this->extra('case');
		/** @var string $classSuffix */
		$classSuffix = df_class_last($this);
		/**
		 * 2016-08-28
		 * Если у класса Webhook нет подклассов,
		 * то не используем суффикс Webhook в именах файлах тестовых данных,
		 * а случай confirm делаем случаем по умолчанию.
		 * /dfe-allpay/confirm/?class=BankCard => AllPay/BankCard.json
		 * /dfe-allpay/confirm/?class=BankCard&case=failure => AllPay/BankCard-failure.json
		 * /dfe-securepay/confirm/?dfTest=1 => SecurePay/confirm.json
		 */
		if ($classSuffix === df_class_last(__CLASS__)) {
			$classSuffix = null;
			$case = $case ?: 'confirm';
		}
		return df_ccc('-', $classSuffix, $case);
	}

	/**
	 * 2017-01-02
	 * @used-by isSuccessful()
	 * @used-by logTitleSuffix()
	 * @return string
	 */
	private function status() {return $this->cv(self::$statusKey);}

	/**
	 * 2016-08-27
	 * @used-by logTitleSuffix()
	 * @used-by \Dfe\SecurePay\Webhook::config()
	 * @var string
	 */
	protected static $readableStatusKey = 'readableStatusKey';

	/**
	 * 2016-08-27
	 * @used-by validate()
	 * @used-by \Dfe\AllPay\Webhook::config()
	 * @used-by \Dfe\SecurePay\Webhook::config()
	 * @var string
	 */
	protected static $signatureKey = 'signatureKey';

	/**
	 * 2016-08-27
	 * @used-by \Dfe\SecurePay\Webhook::config()
	 * @var string
	 */
	protected static $statusExpected = 'statusExpected';

	/**
	 * 2016-08-27
	 * @used-by status()
	 * @used-by \Dfe\AllPay\Webhook::config()
	 * @used-by \Dfe\SecurePay\Webhook::config()
	 * @var string
	 */
	protected static $statusKey = 'statusKey';
}
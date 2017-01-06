<?php
namespace Df\PaypalClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2016-07-10
	 * 2016-12-31
	 * Возвращает идентификатор текущего платежа в платёжной системе.
	 * Этот идентификатор мы используем двояко:
	 * 1) Для формирования нашего внутреннего идентификатора транзакции:
	 * @used-by id()
	 * 2) Для отображения администратору магазина
	 * (при возможности — с прямой ссылкой на страницу платежа
	 * в личном кабинете магазина в платёжной системе):
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @return string
	 */
	final public function externalId() {return $this->cv(self::$externalIdKey);}

	/**
	 * 2017-01-06
	 * Преобразует в глобальный внутренний идентификатор родительской транзакции:
	 *
	 * 1) Идентификатор платежа в платёжной системе.
	 * Это случай Stripe-подобных платёжных систем: у них идентификатор формируется платёжной системой.
	 *
	 * 2) Локальный внутренний идентификатор родительской транзакции.
	 * Это случай PayPal-подобных платёжных систем, когда мы сами ранее сформировали
	 * идентификатор запроса к платёжной системе (этот запрос и является родительской транзакцией).
	 * Мы намеренно передавали идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * Такой идентификатор формируется в методах:
	 * @see \Df\PaypalClone\Charge::requestId()
	 * @see \Dfe\AllPay\Charge::requestId()
	 * Глобальный внутренний идентификатор отличается наличием приставки «<имя модуля>-».
	 *
	 * @override
	 * @see \Df\Payment\Webhook::adaptParentId()
	 * @used-by \Df\Payment\Webhook::parentId()
	 * @param string $id
	 * @return string
	 */
	final protected function adaptParentId($id) {return $this->produceId($id);}

	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\Webhook::id()
	 * @used-by \Df\Payment\Webhook::addTransaction()
	 * @see \Dfe\AllPay\Webhook\Offline::id()
	 * @return string
	 */
	protected function id() {return $this->produceId($this->externalId());}

	/**
	 * 2016-08-27
	 * Раньше метод isSuccessful() вызывался из метода @see validate().
	 * Отныне же @see validate() проверяет, корректно ли сообщение от платёжной системы.
	 * Даже если оплата завершилась отказом покупателя, но оповещение об этом корректно,
	 * то @see validate() вернёт true.
	 * isSuccessful() же проверяет, прошла ли оплата успешно.
	 * 2017-01-06
	 * Кэшировать результат этого метода не нужно, потому что он вызывается лишь единократно:
	 * @used-by \Df\PaypalClone\Confirmation::_handle()
	 * @return bool
	 */
	final protected function isSuccessful() {return
		strval($this->statusExpected()) === strval($this->status())
	;}

	/**
	 * 2017-01-02
	 * @override
	 * @see \Df\Payment\Webhook::logTitleSuffix()
	 * @used-by \Df\Payment\Webhook::log()
	 * @return string|null
	 */
	final protected function logTitleSuffix() {return
		$this->cvo(self::$readableStatusKey, $this->status())
	;}

	/**
	 * 2016-08-29
	 * @override
	 * Потомки перекрывают этот метод, когда ключ идентификатора запроса в запросе
	 * не совпадает с ключем идентификатора запроса в ответе.
	 * Так, в частности, происходит в модуле SecurePay:
	 * @see \Dfe\SecurePay\Charge::requestIdKey()
	 * @see \Dfe\SecurePay\Webhook::parentIdRawKey()
	 * @uses \Df\PaypalClone\ICharge::requestIdKey()
	 * @used-by \Df\Payment\Webhook::parentIdRaw()
	 * @return string
	 */
	protected function parentIdRawKey() {return df_con_s($this, 'Charge', 'requestIdKey');}

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
	final protected function validate() {
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
	 * 2017-01-06
	 * Преобразует в глобальный внутренний идентификатор транзакции:
	 * 1) Внешний идентификатор транзакции.
	 * Это случай, когда идентификатор формируется платёжной системой.
	 * 2) Локальный внутренний идентификатор транзакции.
	 * Это случай, когда мы сами ранее сформировали идентификатор запроса к платёжной системе.
	 * Мы намеренно передавали идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * Такой идентификатор формируется в методах:
	 * @see \Df\PaypalClone\Charge::requestId()
	 * @see \Dfe\AllPay\Charge::requestId()
	 *
	 * Глобальный внутренний идентификатор отличается наличием приставки «<имя модуля>-».
	 *
	 * @used-by adaptParentId()
	 * @used-by id()
	 * @uses \Df\PaypalClone\Method::e2i()
	 * @param string $id
	 * @return string
	 */
	private function produceId($id) {return dfp_method_call_s($this, 'e2i', $id);}

	/**
	 * 2017-01-02
	 * @used-by isSuccessful()
	 * @used-by logTitleSuffix()
	 * @return string
	 */
	private function status() {return $this->cv(self::$statusKey);}

	/**
	 * 2016-08-27
	 * 2016-12-31
	 * Название ключа в сообщении от платёжной системы,
	 * содержащего идентификатор платежа в платёжной системе.
	 * @used-by externalId()
	 * @var string
	 */
	protected static $externalIdKey = 'externalIdKey';

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
	 * @see statusExpected()
	 * @used-by \Dfe\AllPay\Webhook::config()
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
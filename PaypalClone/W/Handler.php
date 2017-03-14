<?php
namespace Df\PaypalClone\W;
use Df\PaypalClone\Signer;
/**
 * 2016-07-09
 * @see \Df\PaypalClone\W\Confirmation
 */
abstract class Handler extends \Df\Payment\W\Handler {
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
	final function externalId() {return $this->cv(self::$externalIdKey);}

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
	 * @see \Df\Payment\W\Handler::adaptParentId()
	 * @used-by \Df\Payment\W\Handler::parentId()
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
	 * @see \Df\Payment\W\Handler::id()
	 * @used-by \Df\Payment\W\Handler::initTransaction()
	 * @see \Dfe\AllPay\W\Handler\Offline::id()
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
	 * @used-by \Df\PaypalClone\W\Confirmation::_handle()
	 * @return bool
	 */
	final protected function isSuccessful() {return
		strval($this->statusExpected()) === strval($this->status())
	;}

	/**
	 * 2017-01-02
	 * @override
	 * @see \Df\Payment\W\Handler::logTitleSuffix()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @return string|null
	 */
	final protected function logTitleSuffix() {return $this->cvo(
		self::$readableStatusKey, $this->status()
	);}

	/**
	 * 2016-08-27
	 * @used-by isSuccessful()
	 * @used-by \Dfe\AllPay\W\Handler\Offline::statusExpected()
	 * @see \Dfe\AllPay\W\Handler\Offline::statusExpected()
	 * @return string|int
	 */
	protected function statusExpected() {return $this->c();}

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
		if (($e = Signer::signResponse($this, $this->r())) !== ($p = $this->cv(self::$signatureKey))) {
			df_error("Invalid signature.\nExpected: «{$e}».\nProvided: «{$p}».");
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
	 * @used-by \Dfe\SecurePay\W\Handler::config()
	 * @var string
	 */
	protected static $readableStatusKey = 'readableStatusKey';

	/**
	 * 2016-08-27
	 * @used-by validate()
	 * @used-by \Dfe\AllPay\W\Handler::config()
	 * @used-by \Dfe\SecurePay\W\Handler::config()
	 * @var string
	 */
	protected static $signatureKey = 'signatureKey';

	/**
	 * 2016-08-27
	 * @see statusExpected()
	 * @used-by \Dfe\AllPay\W\Handler::config()
	 * @used-by \Dfe\SecurePay\W\Handler::config()
	 * @var string
	 */
	protected static $statusExpected = 'statusExpected';

	/**
	 * 2016-08-27
	 * @used-by status()
	 * @used-by \Dfe\AllPay\W\Handler::config()
	 * @used-by \Dfe\SecurePay\W\Handler::config()
	 * @var string
	 */
	protected static $statusKey = 'statusKey';
}
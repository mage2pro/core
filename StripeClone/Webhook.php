<?php
// 2016-12-26
namespace Df\StripeClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2017-01-04
	 * @used-by ro()
	 * @return string
	 */
	abstract protected function roPrefix();

	/**
	 * 2017-01-04
	 * @used-by \Df\StripeClone\WebhookF::i()
	 * @param string $v
	 * @return void
	 */
	final public function typeSet($v) {$this->_type = $v;}

	/**
	 * 2016-12-26
	 * @override
	 * @see \Df\Payment\Webhook::config()
	 * @used-by \Df\Payment\Webhook::configCached()
	 * @return array(string => mixed)
	 */
	protected function config() {return [self::$externalIdKey => $this->parentIdRawKey()];}

	/**
	 * 2017-01-04
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @override
	 * @see \Df\Payment\Webhook::e2i()
	 * @used-by \Df\Payment\Webhook::id()
	 * @uses \Df\StripeClone\Method::e2i()
	 * @param string $externalId
	 * @return string
	 */
	final protected function e2i($externalId) {return dfp_method_call_s($this, 'e2i', $externalId);}

	/**
	 * 2017-01-04
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final public function ro($k = null, $d = null) {return $this->req("{$this->roPrefix()}/$k", $d);}

	/**
	 * 2017-01-04
	 * Для Stripe-подобные платёжных систем
	 * наш внутренний идентификатор транзакции основывается на внешнем:
	 * <имя модуля>-<внешний идентификатор>-<окончание типа события>.
	 * @override
	 * @see \Df\Payment\Webhook::parentIdRawKey()
	 * @used-by \Df\Payment\Webhook::parentIdRaw()
	 * @return string
	 */
	final protected function parentIdRawKey() {return 'id';}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::testDataFile()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function testDataFile() {return $this->type();}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\PaypalClone\Confirmation::type()
	 * @used-by \Df\Payment\Webhook::typeLabel()
	 * @used-by \Dfe\AllPay\Webhook::classSuffix()
	 * @used-by \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	final protected function type() {return $this->_type;}

	/**
	 * 2017-01-04
	 * @var string
	 */
	private $_type;
}
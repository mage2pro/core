<?php
// 2017-01-02
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Framework\Request as Req;
use Df\Payment\Exception\Webhook\NotImplemented;
/**
 * 2017-01-08
 * Потомки:
 * @see \Df\StripeClone\WebhookF
 * @see \Dfe\AllPay\WebhookF
 */
class WebhookF {
	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\PaypalClone\Method::responses()
	 * @used-by \Df\StripeClone\WebhookF::__construct()
	 * @see \Df\StripeClone\WebhookF::__construct()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 */
	public function __construct($module, $req = null) {
		$this->_module = $module;
		$this->_extra = !is_null($req) ? [] : Req::extra();
		$this->_req = !is_null($req) ? $req : $this->reqFromHttp();
	}

	/**
	 * 2017-01-02
	 * Нельзя вместо $module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда i() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\PaypalClone\Method::responses()
	 * @see \Df\StripeClone\WebhookF::i()
	 * 2017-01-04
	 * Отныне null в качестве значения $req означает,
	 * что $req и $extra должны быть взяты из запроса HTTP,
	 * а массив в качестве значения $req означает прямую инициализацию $req:
	 * это сценарий @used-by \Df\PaypalClone\Method::responses()
	 * @return Webhook
	 */
	public function i() {
		/** @var string $class */
		$class = $this->_class();
		return new $class($this->req(), $this->extra());
	}

	/**
	 * 2017-01-02
	 * @used-by i()
	 * @see \Dfe\AllPay\WebhookF::_class()
	 * @see \Df\StripeClone\WebhookF::_class()
	 * Нельзя вместо $module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда i() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @return DFE|NotImplemented
	 */
	protected function _class() {return df_con($this->_module, 'Webhook');}

	/**
	 * 2017-01-07
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @used-by \Df\StripeClone\WebhookF::_class()
	 * @param string|null $type
	 * @return string
	 * @throws DFE
	 */
	final protected function assertType($type) {
		if (!$type) {
			$this->eRequestIsInvalid('it does not specify its type');
		}
		return $type;
	}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\WebhookF::_class()
	 * @used-by assertType()
	 * @param string $reason
	 * @throws DFE
	 */
	final protected function eRequestIsInvalid($reason) {
		df_error("The request is invalid because $reason.")
	;}

	/**
	 * 2017-01-07
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function extra($k = null, $d = null) {return dfak($this->_extra, $k, $d);}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\WebhookF::_class()
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @return string|object
	 */
	final protected function module() {return $this->_module;}

	/**
	 * 2017-01-07
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function req($k = null, $d = null) {return dfak($this->_req, $k, $d);}

	/**
	 * 2017-01-04
	 * @used-by __construct()
	 * @see \Df\StripeClone\WebhookF::reqFromHttp()
	 * @return array(string => mixed)
	 */
	protected function reqFromHttp() {return Req::clean();}

	/**
	 * 2017-01-07
	 * @used-by __construct()
	 * @var array(string => mixed)
	 */
	private $_extra;

	/**
	 * 2017-01-07
	 * @used-by __construct()
	 * @used-by _class()
	 * @var string|object
	 */
	private $_module;

	/**
	 * 2017-01-07
	 * @used-by __construct()
	 * @var array(string => mixed)
	 */
	private $_req;
}
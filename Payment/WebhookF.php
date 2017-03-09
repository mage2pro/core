<?php
// 2017-01-02
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Framework\Request as Req;
use Df\Payment\Exception\Webhook\Factory as EFactory;
use Df\Payment\Exception\Webhook\NotImplemented;
/**
 * 2017-01-08
 * @see \Df\Payment\WebhookF\Json
 * @see \Dfe\AllPay\WebhookF
 */
class WebhookF {
	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\PaypalClone\TM::responses()
	 * @see \Df\Payment\WebhookF\Json::__construct()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 * null в качестве значения $req означает,
	 * что $req и $extra должны быть взяты из запроса HTTP,
	 * а массив в качестве значения $req означает прямую инициализацию $req:
	 * это сценарий @used-by \Df\PaypalClone\TM::responses()
	 */
	function __construct($module, $req = null) {
		$this->_module = $module;
		$this->_extra = !is_null($req) ? [] : Req::extra();
		$this->_req = !is_null($req) ? $req : $this->reqFromHttp();
	}

	/**
	 * 2017-01-02
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\PaypalClone\TM::responses()
	 * @see \Df\Payment\WebhookF\Json::i()
	 * @return Webhook
	 */
	function i() {
		/**
		 * 2017-01-11
		 * Добавил эту проверку, чтобы дать разработчику более понятное диагностическое сообщение,
		 * нежели стандартное. Класс Webook может получиться абстрактным,
		 * если по ошибке разработчика система создала неверную фабрику.
		 * @see \Df\Payment\Action\Webhook::execute()
		 * @var string $c
		 */
		if (df_class_check_abstract($c = $this->_class())) {
			$this->error(
				"The webhook class «{$c}» is abstract."
				."\nIs «%s» the right fabric for this webhook?"
				,get_class($this)
			);
		}
		return new $c($this->req(), $this->extra());
	}

	/**
	 * 2017-01-02
	 * @used-by i()
	 * @see \Df\Payment\WebhookF\Json::_class()
	 * @see \Dfe\AllPay\WebhookF::_class()
	 * Нельзя вместо $this->_module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда _class() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @return DFE|NotImplemented
	 */
	protected function _class() {return df_con($this->_module, 'Webhook');}

	/**
	 * 2017-01-07
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @used-by \Df\Payment\WebhookF\Json::_class()
	 * @param string|null $type
	 * @return string
	 * @throws DFE
	 */
	final protected function assertType($type) {return $type ?: $this->eRequestIsInvalid(
		'it does not specify its own type'
	);}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\WebhookF\Json::_class()
	 * @used-by assertType()
	 * @param string $reason
	 * @throws DFE
	 */
	final protected function eRequestIsInvalid($reason) {
		if ($this->_req) {
			df_sentry_extra($this, 'Request', $this->_req);
		}
		$this->error("The request is invalid because $reason.");
	;}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\WebhookF\Json::type()
	 * @used-by \Dfe\AllPay\WebhookF::_class()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function extra($k = null, $d = null) {return dfak($this->_extra, $k, $d);}

	/**
	 * 2017-01-07
	 * @used-by \Df\Payment\WebhookF\Json::_class()
	 * @used-by \Df\Payment\WebhookF\Json::ss()
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
	 * @see \Df\Payment\WebhookF\Json::reqFromHttp()
	 * @return array(string => mixed)
	 */
	protected function reqFromHttp() {return Req::clean();}

	/**
	 * 2017-01-11
	 * @used-by eRequestIsInvalid()
	 * @used-by i()
	 * @param array ...$args
	 * @throws EFactory
	 */
	private function error(...$args) {throw new EFactory($this->req(), df_format(func_get_args()));}

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
	 * @used-by eRequestIsInvalid()
	 * @used-by req()
	 * @var array(string => mixed)
	 */
	private $_req;
}
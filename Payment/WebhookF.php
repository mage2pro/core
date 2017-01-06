<?php
// 2017-01-02
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Df\Framework\Request as Req;
use Df\Payment\Exception\Webhook\NotImplemented;
class WebhookF {
	/**
	 * 2017-01-02
	 * Нельзя вместо $module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда i() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\PaypalClone\Method::responses()
	 * @see \Df\StripeClone\WebhookF::i()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 * 2017-01-04
	 * Отныне null в качестве значения $req означает,
	 * что $req и $extra должны быть взяты из запроса HTTP,
	 * а массив в качестве значения $req означает прямую инициализацию $req:
	 * это сценарий @used-by \Df\PaypalClone\Method::responses()
	 * @return Webhook
	 */
	public function i($module, $req = null) {
		/** @var array(string => string) $extra */
		list($req, $extra) = !is_null($req) ? [$req, []] : [$this->req(), Req::extra()];
		/** @var string $class */
		$class = $this->_class($module, $req, $extra);
		return new $class($req, $extra);
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
	 * @param string|object $module
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 * @return DFE|NotImplemented
	 */
	protected function _class($module, array $req, array $extra = []) {return
		df_con($module, 'Webhook')
	;}

	/**
	 * 2017-01-04
	 * @used-by i()
	 * @see \Df\StripeClone\WebhookF::req()
	 * @return array(string => mixed)
	 */
	protected function req() {return Req::clean();}
}
<?php
// 2017-01-02
namespace Df\Payment;
class WebhookF {
	/**
	 * 2017-01-02
	 * Нельзя вместо $module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда i() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @param string|object $module
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 * @return Webhook
	 */
	final public function i($module, array $req, array $extra = []) {
		/** @var string $class */
		$class = $this->_class($module, $req, $extra);
		return new $class($req, $extra);
	}

	/**
	 * 2017-01-02
	 * @see i()
	 * Нельзя вместо $module использовать $this, потому что не все модули имеют фабрики.
	 * Например, модуль SecurePay фабрики не имеет,
	 * и тогда i() должна вернуть не @see \Df\Payment\Webhook
	 * (как получилось бы при использовании $this), а @see \Dfe\SecurePay\Webhook
	 * @param string|object $module
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 * @return string
	 */
	protected function _class($module, array $req, array $extra = []) {return
		df_con($module, 'Webhook')
	;}
}
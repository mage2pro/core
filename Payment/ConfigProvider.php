<?php
namespace Df\Payment;
use Magento\Checkout\Model\ConfigProviderInterface;
// 2016-08-04
abstract class ConfigProvider implements ConfigProviderInterface {
	/**
	 * 2016-02-27
	 * @override
	 * @see \Magento\Checkout\Model\ConfigProviderInterface::getConfig()
	 * https://github.com/magento/magento2/blob/cf7df72/app/code/Magento/Checkout/Model/ConfigProviderInterface.php#L15-L20
	 * @return array(string => mixed)
	 */
	final public function getConfig() {
		return  ['payment' => [$this->code() => !$this->s()->enable() ? [] : $this->custom() + [
			'isActive' => $this->s()->enable()
			,'isTest' => $this->s()->test()
			,'route' => $this->route()
			,'titleBackend' => $this->callS('titleBackendS')
		]]];
	}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	protected function custom() {return [];}

	/**
	 * 2016-08-06
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return string
	 */
	protected function route() {return str_replace('_', '-', $this->code());}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\PlaceOrderInternal::ss()
	 * @param string $key [optional]
	 * @param mixed|callable $default [optional]
	 * @return Settings|mixed
	 */
	protected function s($key = '', $default = null) {
		return Settings::convention($this, $key, null, $default);
	}

	/**
	 * 2016-08-06
	 * @param string $method
	 * @return mixed
	 */
	private function callS($method) {return call_user_func([$this->methodS(), $method]);}

	/**
	 * 2016-08-06
	 * @return string
	 */
	private function code() {
		if (!isset($this->{__METHOD__})) {
			/** @uses \Df\Payment\Method::codeS() */
			$this->{__METHOD__} = $this->callS('codeS');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-06
	 * @return string
	 */
	private function methodS() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_convention($this, 'Method');
		}
		return $this->{__METHOD__};
	}
}
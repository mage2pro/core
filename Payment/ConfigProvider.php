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
		/** @var string $code */
		/** @uses \Df\Payment\Method::codeS() */
		$code = call_user_func([df_convention($this, 'Method'), 'codeS']);
		return ['payment' => [$code => $this->custom() + [
			'isActive' => $this->s()->enable()
			,'isTest' => $this->s()->test()
		]]];
	}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	protected function custom() {return [];}

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
}
<?php
namespace Df\Shipping;
use Df\Shipping\Settings as S;
use Magento\Checkout\Model\ConfigProviderInterface as IConfigProvider;
# 2018-04-23
/** @see \Doormall\Shipping\ConfigProvider */
abstract class ConfigProvider implements IConfigProvider, \Df\Config\ISettings {
	/**
	 * 2018-04-23
	 * @used-by self::getConfig()
	 * @see \Doormall\Shipping\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	abstract protected function config():array;

	/**
	 * 2017-03-03
	 * 2017-08-09 We can safely mark this method as «final» because the implemented interface does not have it.
	 * https://github.com/mage2pro/core/issues/20
	 */
	final function __construct(string $m = '') {$this->_mc = df_module_name_c($m ?: $this);}

	/**
	 * 2016-02-27
	 * 2016-08-24
	 * Этот метод вызывается не только на странице оформления заказа, но и на странице корзицы.
	 * Однако нам на странице корзины не нужно вычислять настройки наших способов оплаты:
	 * ведь они там не отображаются, а вычисление настрое расходует ресурсы:
	 * в частности, мой модуль Stripe при этом делает 2 запроса к API Stripe.
	 * Поэтому на странице корзины ничего не делаем:
	 * Magento потом всё равно вызовет этот метод повторно на странице оформления заказа.
	 *
	 * Обратите внимание, что оформление заказа состоит из нескольких шагов,
	 * но переключение между ними происходит без перезагрузки страницы,
	 * поэтому этот метод вызывается лишь единожды на самом первом шаге
	 * (обычно это шаг выбора адреса и способа доставки).
	 *
	 * Обеспечиваем наличие ключа «payment»,
	 * чтобы не приходилось проверять его наличие на стороне JavaScript.
	 *
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see IConfigProvider::getConfig()
	 * @used-by self::p()
	 * @used-by \Magento\Checkout\Model\CompositeConfigProvider::getConfig():
	 *		public function getConfig() {
	 *			$config = [];
	 *			foreach ($this->configProviders as $configProvider) {
	 *				$config = array_merge_recursive($config, $configProvider->getConfig());
	 *			}
	 *			return $config;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Checkout/Model/CompositeConfigProvider.php#L31-L41
	 * @return array(string => mixed)
	 */
	function getConfig():array {return ['shipping' => !df_is_checkout() || !$this->s()->enable() ? [] : [
		$this->m()->getCarrierCode() => $this->config()
	]];}

	/**
	 * 2016-08-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * 2023-01-28
	 * «Declaration of Df\Shipping\ConfigProvider::s(): Df\Shipping\Settings
	 * must be compatible with Df\Config\ISettings::s(): Df\Config\Settings»:
	 * https://github.com/mage2pro/core/issues/183
	 * @override
	 * @see \Df\Config\ISettings::s()
	 * @used-by self::getConfig()
	 * @return S
	 */
	function s():\Df\Config\Settings {return $this->m()->s();}

	/**
	 * 2017-02-07
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by self::getConfig()
	 */
	protected function m():Method {return dfc($this, function() {return dfsm($this->_mc);});}

	/**
	 * 2017-03-03
	 * @used-by self::__construct()
	 * @used-by self::m()
	 * @used-by self::s()
	 * @var string
	 */
	private $_mc;
}
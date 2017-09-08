<?php
namespace Df\Payment;
use Df\Payment\Settings as S;
use Magento\Checkout\Model\ConfigProviderInterface as IConfigProvider;
/**
 * 2016-08-04
 * @see \Df\GingerPaymentsBase\ConfigProvider
 * @see \Df\Payment\ConfigProvider\BankCard
 * @see \Dfe\AllPay\ConfigProvider
 * @see \Dfe\Dragonpay\ConfigProvider
 * @see \Dfe\IPay88\ConfigProvider
 * @see \Dfe\Klarna\ConfigProvider
 * @see \Dfe\MPay24\ConfigProvider
 * @see \Dfe\Paypal\ConfigProvider
 * @see \Dfe\Paystation\ConfigProvider
 * @see \Dfe\PostFinance\ConfigProvider
 * @see \Dfe\Qiwi\ConfigProvider
 * @see \Dfe\Robokassa\ConfigProvider
 * @see \Dfe\Tinkoff\ConfigProvider
 * @see \Dfe\YandexKassa\ConfigProvider
 * 2017-03-03 The class is not abstract anymore: you can use it as a base for a virtual type.
 * 2017-04-03
 * Раньше этот класс использоваться как основа для вирутального класса Klarna:
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L13-L15
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L9
 */
class ConfigProvider implements IConfigProvider {
	/**
	 * 2017-03-03
	 * 2017-08-09 We can safely mark this method as «final» because the implemented interface does not have it.
	 * https://github.com/mage2pro/core/issues/20
	 * @param string|null $module [optional]
	 */
	final function __construct($module = null) {$this->_mc = df_module_name_c($module ?: $this);}

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
	 * @used-by p()
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
	function getConfig() {return ['payment' =>
		!(df_is_checkout() || df_is_checkout_multishipping()) || !$this->s()->enable()
		? [] : [$this->m()->getCode() => $this->config()
	]];}

	/**
	 * 2017-04-17 The result amount is in the payment currency.
	 * @used-by config()
	 * @used-by \Dfe\Robokassa\ConfigProvider::config()
	 * @return float
	 */
	final protected function amount() {return dfc($this, function() {return $this->s()->cFromOrder(
		df_quote()->getGrandTotal(), df_quote()
	);});}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Dfe\AllPay\ConfigProvider::config()
	 * @see \Dfe\IPay88\ConfigProvider::config()
	 * @see \Dfe\Robokassa\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config() {/** @var S $s */ $s = $this->s(); return [
		'amountF' => $this->m()->amountFormat($this->amount())
		,'requireBillingAddress' => $s->requireBillingAddress()
		,'isTest' => $s->test()
		// 2017-02-07
		// https://github.com/mage2pro/core/blob/1.12.7/Payment/view/frontend/web/mixin.js?ts=4#L249-L258
		,'paymentCurrency' => [
			// 2016-09-06
			// Код платёжной валюты.
			// Это значение индивидуально для каждого платёжного модуля.
			// 2017-02-07
			// https://github.com/mage2pro/2checkout/blob/1.1.18/view/frontend/web/main.js?ts=4#L23
			// https://github.com/mage2pro/paymill/blob/0.1.2/view/frontend/web/main.js?ts=4#L46
			'code' => $s->currencyC()
			// 2016-09-06
			// Правила форматирования платёжной валюты.
			// How to get the display format for a particular currency and locale programmatically?
			// https://mage2.pro/t/2022
			// 2017-02-07
			// https://github.com/mage2pro/core/blob/1.12.9/Payment/view/frontend/web/mixin.js?ts=4#L205
			,'format' => df_locale_f()->getPriceFormat($locale = null, $s->currencyC())
			// 2017-02-07
			// https://github.com/checkout/checkout-magento2-plugin/blob/1.1.21/view/frontend/web/main.js?ts=4#L27
			// https://github.com/mage2pro/2checkout/blob/1.1.18/view/frontend/web/main.js?ts=4#L26
			// https://github.com/mage2pro/securepay/blob/1.1.19/view/frontend/web/main.js?ts=4#L37
			// https://github.com/mage2pro/securepay/blob/1.1.19/view/frontend/web/main.js?ts=4#L51
			,'name' => $s->currencyN()
			// 2016-09-06
			// Курс обмена учётной валюты на платёжную.
			// Это значение индивидуально для каждого платёжного модуля.
			// 2017-02-07
			// https://github.com/mage2pro/core/blob/1.12.7/Payment/view/frontend/web/mixin.js?ts=4#L60
			,'rate' => $s->cRateToPayment()
		]
		,'titleBackend' => $this->m()->titleB()
	];}

	/**
	 * 2017-02-07
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by config()
	 * @used-by getConfig()
	 * @used-by p()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @return Method
	 */
	protected function m() {return dfc($this, function() {return dfpmq($this->_mc);});}

	/**
	 * 2016-08-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by config()
	 * @return S
	 */
	protected function s() {return $this->m()->s();}

	/**
	 * 2017-03-03
	 * @used-by __construct()
	 * @used-by m()
	 * @used-by s()
	 * @var string
	 */
	private $_mc;

	/**
	 * 2017-08-25
	 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
	 * @return array(string => mixed)
	 */
	final static function p() {
		$i = df_new_om(static::class); /** @var self $i */
		return dfa_deep($i->getConfig(), "payment/{$i->m()->getCode()}");
	}
}
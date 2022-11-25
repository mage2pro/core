<?php
namespace Df\Payment;
use Df\Payment\ConfigProvider\IOptions;
use Df\Payment\Settings as S;
use Df\Payment\Settings\Options;
use Df\Payment\Source\Options\DisplayMode;
use Magento\Checkout\Model\ConfigProviderInterface as IConfigProvider;
/**
 * 2016-08-04
 * @see \Df\GingerPaymentsBase\ConfigProvider
 * @see \Df\StripeClone\ConfigProvider
 * @see \Df\Payment\ConfigProvider\BankCard
 * @see \Dfe\ACH\ConfigProvider
 * @see \Dfe\AllPay\ConfigProvider
 * @see \Dfe\AlphaCommerceHub\ConfigProvider
 * Dragonpay: https://github.com/mage2pro/dragonpay/blob/1.1.4/etc/frontend/di.xml#L13-L15
 * @see \Dfe\IPay88\ConfigProvider
 * @see \Dfe\Klarna\ConfigProvider
 * PostFinance: https://github.com/mage2pro/postfinance/blob/1.0.9/etc/frontend/di.xml#L13-L15
 * Qiwi: https://github.com/mage2pro/qiwi/blob/1.0.7/etc/frontend/di.xml#L13-L15
 * @see \Dfe\Robokassa\ConfigProvider
 * TBC Bank: https://github.com/mage2pro/tbc-bank/blob/1.4.2/etc/frontend/di.xml#L20-L22
 * Vantiv: https://github.com/mage2pro/vantiv/blob/1.1.2/etc/frontend/di.xml#L20-L22
 * @see \Dfe\YandexKassa\ConfigProvider
 * 2017-03-03 The class is not abstract anymore: you can use it as a base for a virtual type.
 * 2017-04-03
 * Раньше этот класс использоваться как основа для вирутального класса Klarna:
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L13-L15
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L9
 */
class ConfigProvider implements IConfigProvider, \Df\Config\ISettings {
	/**
	 * 2017-03-03
	 * 2017-08-09 We can safely mark this method as «final» because the implemented interface does not have it.
	 * https://github.com/mage2pro/core/issues/20
	 * @param string|null $m [optional]
	 */
	final function __construct($m = null) {$this->_mc = df_module_name_c($m ?: $this);}

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
	function getConfig():array {return ['payment' =>
		!df_is_checkout() || !$this->s()->enable() ? [] : [$this->m()->getCode() => $this->config()]
	];}

	/**
	 * 2016-08-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Config\ISettings::s()
	 * @used-by self::config()
	 * @used-by self::configOptions()
	 * @used-by self::getConfig()
	 * @used-by \Dfe\ACH\ConfigProvider::config()
	 * @used-by \Dfe\AllPay\ConfigProvider::options()
	 * @used-by \Dfe\IPay88\ConfigProvider::options()
	 * @used-by \Dfe\YandexKassa\ConfigProvider::options()
	 */
	function s():S {return $this->m()->s();}

	/**
	 * 2017-04-17 The result amount is in the payment currency.
	 * @used-by self::config()
	 * @used-by \Dfe\Robokassa\ConfigProvider::options()
	 */
	final protected function amount():float {return dfc($this, function() {return $this->currency()->fromOrder(
		df_quote()->getGrandTotal(), df_quote()
	);});}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Df\GingerPaymentsBase\ConfigProvider::config()
	 * @see \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Df\StripeClone\ConfigProvider::config()
	 * @see \Dfe\ACH\ConfigProvider::config()
	 * @see \Dfe\AllPay\ConfigProvider::config()
	 * @see \Dfe\AlphaCommerceHub\ConfigProvider::config()
	 * @see \Dfe\IPay88\ConfigProvider::config()
	 * @see \Dfe\Robokassa\ConfigProvider::config()
	 * @see \Dfe\YandexKassa\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config():array {
		$s = $this->s(); /** @var S $s */
		$currency = $this->currency(); /** @var Currency $currency */
		$currencyC = $currency->iso3(); /** @var string $currencyC */
		return [
			/**
			 * 2017-11-05
			 * @todo «Passing `amountF` to the client side is incorrect, because a payment's amount could be changed client-side
			 * (e.g., after a discount code application)» https://github.com/mage2pro/core/issues/45
			 */
			'amountF' => $this->m()->amountFormat($this->amount())
			,'requireBillingAddress' => $s->requireBillingAddress()
			,'isTest' => $s->test()
			# 2017-02-07 https://github.com/mage2pro/core/blob/1.12.7/Payment/view/frontend/web/mixin.js?ts=4#L249-L258
			,'paymentCurrency' => [
				# 2016-09-06
				# Код платёжной валюты.
				# Это значение индивидуально для каждого платёжного модуля.
				# 2017-02-07
				# https://github.com/mage2pro/2checkout/blob/1.1.18/view/frontend/web/main.js?ts=4#L23
				# https://github.com/mage2pro/paymill/blob/0.1.2/view/frontend/web/main.js?ts=4#L46
				'code' => $currencyC
				# 2016-09-06
				# Правила форматирования платёжной валюты.
				# How to get the display format for a particular currency and locale programmatically?
				# https://mage2.pro/t/2022
				# 2017-02-07
				# https://github.com/mage2pro/core/blob/1.12.9/Payment/view/frontend/web/mixin.js?ts=4#L205
				,'format' => df_locale_f()->getPriceFormat($locale = null, $currencyC)
				# 2017-02-07
				# https://github.com/checkout/checkout-magento2-plugin/blob/1.1.21/view/frontend/web/main.js?ts=4#L27
				# https://github.com/mage2pro/2checkout/blob/1.1.18/view/frontend/web/main.js?ts=4#L26
				# https://github.com/mage2pro/securepay/blob/1.1.19/view/frontend/web/main.js?ts=4#L37
				# https://github.com/mage2pro/securepay/blob/1.1.19/view/frontend/web/main.js?ts=4#L51
				,'name' => df_currency_name($currencyC)
				# 2016-09-06
				# Курс обмена учётной валюты на платёжную.
				# Это значение индивидуально для каждого платёжного модуля.
				# 2017-02-07
				# https://github.com/mage2pro/core/blob/1.12.7/Payment/view/frontend/web/mixin.js?ts=4#L60
				,'rate' => $currency->rateToPayment()
			]
			,'titleBackend' => $this->m()->titleB()
		]
	;}

	/**
	 * 2017-02-07
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by self::config()
	 * @used-by self::getConfig()
	 * @used-by self::p()
	 * @used-by \Df\Payment\ConfigProvider::currency()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Dfe\AlphaCommerceHub\ConfigProvider::option()
	 * @used-by \Dfe\AlphaCommerceHub\ConfigProvider::config()
	 */
	protected function m():Method {return dfc($this, function() {return dfpmq($this->_mc);});}

	/**
	 * 2017-10-12
	 * @used-by self::amount()
	 * @used-by self::config()
	 */
	private function currency():Currency {return dfp_currency($this->m());}

	/**
	 * 2017-03-03
	 * @used-by self::__construct()
	 * @used-by self::m()
	 * @used-by self::s()
	 * @var string
	 */
	private $_mc;

	/**
	 * 2017-08-25
	 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
	 * @return array(string => mixed)
	 */
	final static function p():array {
		$i = df_new_om(static::class); /** @var self $i */
		return dfa_deep($i->getConfig(), "payment/{$i->m()->getCode()}");
	}

	/**
	 * 2017-09-18
	 * @used-by \Df\GingerPaymentsBase\ConfigProvider::config()
	 * @used-by \Dfe\IPay88\ConfigProvider::config()
	 * @used-by \Dfe\Robokassa\ConfigProvider::config()
	 * @used-by \Dfe\YandexKassa\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	final protected static function configOptions(IOptions $o):array {$s = $o->s(); /** @var Settings $s */ return [
		'needShowOptions' => Options::needShow($s) # 2017-09-19 «Where to ask for a payment option?»
		/**
		 * 2017-09-18
		 * @used-by Df_Payments/withOptions::options()
		 * https://github.com/mage2pro/core/blob/2.12.5/Payment/view/frontend/web/withOptions.js#L72-L80
		 * 2017-10-29
		 * Note 1.
		 * «JavaScript does not guarantee the properties order in objects,
		 * so \Df\Payment\ConfigProvider::configOptions() should
		 * specify the payment options orderings exactly in a separate property,
		 * or pass the options to the client side as an array instead of an object»:
		 * https://github.com/mage2pro/core/issues/41
		 * Note 2.
		 * It is important to use @uses array_values()
		 * for the result to be interpreted as an array? not object, on the client side.
		 */
		,'options' => array_is_list($oo = $o->options()) ? $oo :
			array_values(df_map_k($oo, function($v, $l) {return ['label' => $l, 'value' => $v];}))
		# 2017-09-19 A text to be shown on the Magento checkout page instead of the payment options dialog.
		,'optionsDescription' => $s->v('optionsDescription')
		/**
		 * 2017-09-21
		 * «Payment options display mode» (`images` or `text`).
		 * *) iPay88: https://github.com/mage2pro/ipay88/blob/1.3.3/etc/adminhtml/system.xml#L151-L164
		 * *) Robokassa: https://github.com/mage2pro/robokassa/blob/1.2.4/etc/adminhtml/system.xml#L230-L243
		 * *) Yandex.Kassa: https://github.com/mage2pro/yandex-kassa/blob/0.1.5/etc/adminhtml/system.xml#L178-L192
		 */
		,'optionsDisplayMode' => $s->v('optionsDisplayMode', null, DisplayMode::IMAGES)
		# 2017-09-19 A text above the payment options on the Magento checkout page.
		,'optionsPrompt' => $s->v('optionsPrompt')
	];}
}
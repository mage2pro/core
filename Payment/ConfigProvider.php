<?php
namespace Df\Payment;
use Df\Payment\Settings as S;
use Magento\Checkout\Model\ConfigProviderInterface;
/**
 * 2016-08-04
 * @see \Df\GingerPaymentsBase\ConfigProvider
 * @see \Df\Payment\ConfigProvider\BankCard
 * @see \Dfe\AllPay\ConfigProvider
 *
 * 2017-03-03
 * The class is not abstract anymore: you can use it as a base for a virtual type:
 * 1) Klarna
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L13-L15
 * https://github.com/mage2pro/klarna/blob/0.1.12/etc/frontend/di.xml?ts=4#L9
 */
class ConfigProvider implements ConfigProviderInterface {
	/**
	 * 2017-03-03
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
	 * @used-by \Magento\Checkout\Model\CompositeConfigProvider::getConfig()
	 *
	 * @override
	 * @see \Magento\Checkout\Model\ConfigProviderInterface::getConfig()
	 * https://github.com/magento/magento2/blob/cf7df72/app/code/Magento/Checkout/Model/ConfigProviderInterface.php#L15-L20
	 * @return array(string => mixed)
	 */
	final function getConfig() {return ['payment' =>
		!df_is_checkout() || !$this->s()->enable() ? [] : [$this->m()->getCode() => $this->config()]
	];}

	/**
	 * 2016-08-04
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Df\Payment\ConfigProvider\BankCard::config()
	 * @see \Dfe\AllPay\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config() {/** @var S $s */ $s = $this->s(); return [
		'amountF' => $this->m()->amountFormat($s->cFromOrder(
			df_quote()->getGrandTotal(), df_quote()
		))
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
		,'route' => df_route($this->_mc)
		,'titleBackend' => $this->m()->titleB()
	];}

	/**
	 * 2017-02-07
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by config()
	 * @used-by getConfig()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @return Method
	 */
	protected function m() {return dfc($this, function() {return dfpm($this->_mc);});}

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
	 * @used-by config()
	 * @used-by m()
	 * @used-by s()
	 * @var string
	 */
	private $_mc;
}
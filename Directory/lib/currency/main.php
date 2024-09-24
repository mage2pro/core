<?php
use Df\Directory\Model\Country;
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use NumberFormatter as NF;

/**
 * 2016-07-04 «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * @used-by df_currency_base()
 * @param C|string $c [optional]
 */
function df_currency($c = ''):C {return !$c ? df_currency_base() : ($c instanceof C ? $c :
	dfcf(function(string $c):C {return df_new_om(C::class)->load($c);}, [$c])
);}

/**
 * 2016-07-04 «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 * @used-by df_currency()
 * @used-by df_currency_convert_from_base()
 * @used-by df_currency_convert_to_base()
 * @used-by Df\Payment\Currency::rateToPayment()
 * @used-by Df\Payment\Currency::toBase()
 * @param ScopeA|Store|ConfigData|IConfigData|O|Q|array(int|string)|null|string|int $s [optional]
 */
function df_currency_base($s = null):C {return df_currency(df_assert_sne(df_cfg(
	C::XML_PATH_CURRENCY_BASE, df_is_oq($s) ? $s->getStore() : $s
)));}

/**
 * 2017-01-29
 * «How to get the currency code for a country with PHP?» https://mage2.pro/t/2552
 * http://stackoverflow.com/a/31755693
 * @used-by Df\Directory\Test\currency::t01()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge::currency()
 * @used-by Dfe\Stripe\FE\Currency::currency()
 * @param string|Country $c
 */
function df_currency_by_country_c($c):string {return dfcf(function($c) {return
	(new NF(df_locale_by_country($c), NF::CURRENCY))->getTextAttribute(NF::CURRENCY_CODE)
;}, [df_currency_code($c)]);}

/**
 * 2016-08-08
 * http://magento.stackexchange.com/a/108013
 * В отличие от @see df_currency_base() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @used-by df_currency_current_c()
 * @used-by df_currency_rate_to_current()
 * @param int|string|null|bool|StoreInterface $s [optional]
 */
function df_currency_current($s = null):C {return df_store($s)->getCurrentCurrency();}
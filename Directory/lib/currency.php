<?php
use Df\Directory\Model\Country;
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Sales\Model\Order as O;
use Magento\Quote\Model\Quote as Q;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use NumberFormatter as NF;
/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return string[]
 */
function df_currencies_codes_allowed($s = null) {return df_store($s)->getAvailableCurrencyCodes(true);}

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(string => string)
 */
function df_currencies_ctn($s = null) {return dfcf(function($s = null) {
	$s = df_store($s);
	/** @var C $currency */
	$currency = df_o(C::class);
	/** @var string[] $codes */
	$codes = df_currencies_codes_allowed($s);
	// 2016-02-17
	// $rates ниже не содержит базовую валюту.
	/** @var string $baseCode */
	$baseCode = $s->getBaseCurrency()->getCode();
	/** @var array(string => float) $rates */
	$rates = $currency->getCurrencyRates($s->getBaseCurrency(), $codes);
	/** @var array(string => string) $result */
	$result = [];
	foreach ($codes as $code) {
		/** @var string $code */
		if ($baseCode === $code || isset($rates[$code])) {
			$result[$code] = df_currency_name($code);
		}
	}
	return $result;
}, func_get_args());}

/**
 * 2015-12-28
 * @see df_countries_options()
 * @param string[] $filter [optional]
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(array(string => string))
 */
function df_currencies_options(array $filter = [], $s = null) {return
	dfcf(function(array $filter = [], $s = null) {
		/** @var array(string => string) $all */
		$all = df_currencies_ctn($s);
		return df_map_to_options(!$filter ? $all : dfa_select_ordered($all, $filter));
	}, func_get_args())
;}

/**
 * 2016-07-04
 * «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * @param C|string|null $c [optional]
 * @return C
 */
function df_currency($c = null) {
	/** @var C $result */
	if (!$c) {
		$result = df_currency_base();
	}
	elseif ($c instanceof C) {
		$result = $c;
	}
	else {
		static $cache; /** @var array(string => Currency) $cache */
		if (!isset($cache[$c])) {
			$cache[$c] = df_new_om(C::class)->load($c);
		}
		$result = $cache[$c];
	}
	return $result;
}

/**
 * 2016-07-04
 * «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 *
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 * @used-by \Df\Payment\Currency::rateToPayment()
 * @used-by \Df\Payment\Currency::toBase()
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData|O|Q|array(int|string) $s [optional]
 * @return C
 */
function df_currency_base($s = null) {return df_currency(df_assert_sne(df_cfg(
	C::XML_PATH_CURRENCY_BASE, df_is_oq($s) ? $s->getStore() : $s
)));}

/**
 * 2016-09-05
 * @used-by \Df\Directory\FE\Currency::map()
 * @used-by \Df\Payment\Currency::fromBase()
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return string
 */
function df_currency_base_c($s = null) {return df_currency_base($s)->getCode();}

/**
 * 2017-01-29
 * «How to get the currency code for a country with PHP?» https://mage2.pro/t/2552
 * http://stackoverflow.com/a/31755693
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge::currency()
 * @used-by \Dfe\Stripe\FE\Currency::currency()
 * @param string|Country $c
 * @return string
 */
function df_currency_by_country_c($c) {return dfcf(function($c) {return
	(new NF(df_locale_by_country($c), NF::CURRENCY))->getTextAttribute(NF::CURRENCY_CODE)
;}, [df_currency_code($c)]);}

/**
 * 2016-07-04
 * @param C|string|null $c [optional]
 * @return string
 */
function df_currency_code($c = null) {return df_currency($c)->getCode();}

/**
 * 2016-07-04
 * «How to programmatically convert a money amount from a currency to another one?» https://mage2.pro/t/1842
 * 2016-09-05
 * Обратите внимание, что перевод из одной валюты в другую
 * надо осуществлять только в направлении 'базовая валюта' => 'второстепенная валюта',
 * но не наоборот
 * (Magento не умеет выполнять первод 'второстепенная валюта' => 'базовая валюта'
 * даже при наличии курса 'базовая валюта' => 'второстепенная валюта',
 * и возбуждает исключительную ситуацию).
 *
 * Курс валюты сау на себя в системе всегда есть:
 * @see \Magento\Directory\Model\ResourceModel\Currency::getRate()
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/ResourceModel/Currency.php#L56-L58
 *
 * @uses \Magento\Directory\Model\Currency::convert() прекрасно понимает нулевой $to:
 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Directory/Model/Currency.php#L216-L217
 *
 * @used-by \Df\Payment\Currency::toBase()
 * @used-by \Df\Payment\Currency::toOrder()
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return float
 */
function df_currency_convert($a, $from = null, $to = null, $s = null) {return
	df_currency_convert_from_base(df_currency_convert_to_base($a, $from, $s), $to, $s)
;}

/**
 * 2017-04-15
 * @used-by \Dfe\Stripe\Method::minimumAmount()
 * @used-by \Dfe\TwoCheckout\Method::minimumAmount()
 * @param float $a
 * @param C|string|null $from [optional]
 * @param C|string|null $to [optional]
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return float
 */
function df_currency_convert_safe($a, $from = null, $to = null, $s = null) {return df_try(
	function() use($a, $from, $to, $s) {return df_currency_convert($a, $from, $to, $s);}, $a
);}

/**
 * 2016-09-05
 * @param float $a
 * @param C|string|null $to
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return float
 */
function df_currency_convert_from_base($a, $to, $s = null) {return df_currency_base($s)->convert($a, $to);}

/**
 * 2016-09-05
 * @used-by df_currency_convert()
 * @used-by \Frugue\Shipping\Method::collectRates()
 * @param float $a
 * @param C|string|null $from
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return float
 */
function df_currency_convert_to_base($a, $from, $s = null) {return $a / df_currency_base($s)->convert(1, $from);}

/**
 * 2016-08-08
 * http://magento.stackexchange.com/a/108013
 * В отличие от @see df_currency_base() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return C
 */
function df_currency_current($s = null) {return df_store($s)->getCurrentCurrency();}

/**
 * 2016-09-05
 * В отличие от @see df_currency_base_с() здесь мы вынуждены использовать не $scope, а $store,
 * потому что учётную валюту можно просто считать из настроек,
 * а текущая валюта может меняться динамически (в том числе посетителем магазина и сессией).
 * @used-by \Df\Directory\FE\Currency::map()
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return string
 */
function df_currency_current_c($s = null) {return df_currency_current($s)->getCode();}

/**
 * 2016-06-30
 * «How to programmatically check whether a currency is allowed
 * and has an exchange rate to the base currency?» https://mage2.pro/t/1832
 * @param string $iso3
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return string[]
 */
function df_currency_has_rate($iso3, $s = null) {return !!dfa(df_currencies_ctn($s), $iso3);}

/**
 * 2016-06-30
 * «How to programmatically get a currency's name by its ISO code?» https://mage2.pro/t/1833
 * @used-by \Df\Payment\ConfigProvider::config()
 * @used-by \Dfe\AlphaCommerceHub\W\Event::currencyName()
 * @param string|C|string[]|C[]|null $c [optional]
 * @return string|string[]
 */
function df_currency_name($c = null) {
	/** @var string|string[] $result */
	if (is_array($c)) {
		$result = array_map(__FUNCTION__, $c);
	}
	else {
		static $rb; /** @var \ResourceBundle $rb */
		if (!isset($rb))  {
			$rb = (new CurrencyBundle)->get(df_locale())['Currencies'];
		}
		$code = is_string($c) ? $c : df_currency_code($c); /** @var string $code */
		$result = $rb[$code][1] ?: $code;
	}
	return $result;
}

/**
 * 2016-08-08
 * @return float
 */
function df_currency_rate_to_current() {return df_currency_base()->getRate(df_currency_current());}
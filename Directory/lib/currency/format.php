<?php
use Magento\Directory\Model\Currency as C;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Store\Api\Data\StoreInterface;

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(string => string)
 */
function df_currencies_ctn($s = null) {return dfcf(function($s = null) {
	$s = df_store($s);
	$currency = df_o(C::class); /** @var C $currency */
	$codes = df_currencies_codes_allowed($s); /** @var string[] $codes */
	// 2016-02-17 $rates ниже не содержит базовую валюту.
	$baseCode = $s->getBaseCurrency()->getCode(); /** @var string $baseCode */
	$rates = $currency->getCurrencyRates($s->getBaseCurrency(), $codes); /** @var array(string => float) $rates */
	$r = []; /** @var array(string => string) $r */
	foreach ($codes as $code) { /** @var string $code */
		if ($baseCode === $code || isset($rates[$code])) {
			$r[$code] = df_currency_name($code);
		}
	}
	return $r;
}, func_get_args());}

/**
 * 2015-12-28
 * @see df_countries_options()
 * @param string[] $filter [optional]
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(array(string => string))
 */
function df_currencies_options(array $filter = [], $s = null) {return dfcf(function(array $filter = [], $s = null) {
	$all = df_currencies_ctn($s); /** @var array(string => string) $all */
	return df_map_to_options(!$filter ? $all : dfa_select_ordered($all, $filter));
}, func_get_args());}

/**
 * 2016-06-30
 * «How to programmatically get a currency's name by its ISO code?» https://mage2.pro/t/1833
 * @used-by \Df\Payment\ConfigProvider::config()
 * @used-by \Dfe\AlphaCommerceHub\W\Event::currencyName()
 * @param string|C|string[]|C[]|null $c [optional]
 * @return string|string[]
 */
function df_currency_name($c = null) {/** @var string|string[] $r */
	if (is_array($c)) {
		$r = array_map(__FUNCTION__, $c);
	}
	else {
		static $rb; /** @var \ResourceBundle $rb */
		if (!isset($rb))  {
			$rb = (new CurrencyBundle)->get(df_locale())['Currencies'];
		}
		$code = is_string($c) ? $c : df_currency_code($c); /** @var string $code */
		$r = $rb[$code][1] ?: $code;
	}
	return $r;
}
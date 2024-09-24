<?php
use Magento\Directory\Model\Currency as C;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Framework\Pricing\PriceCurrencyInterface as IPriceCurrency;
use Magento\Store\Api\Data\StoreInterface;

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(string => string)
 */
function df_currencies_ctn($s = null):array {return dfcf(function($s = null) {
	$s = df_store($s);
	$currency = df_o(C::class); /** @var C $currency */
	$codes = df_currencies_codes_allowed($s); /** @var string[] $codes */
	# 2016-02-17 $rates ниже не содержит базовую валюту.
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
 * @param string[] $keys [optional]
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return array(array(string => string))
 */
function df_currencies_options(array $keys = [], $s = null):array {return dfcf(function(array $keys = [], $s = null) {return
	df_map_to_options(dfa(df_currencies_ctn($s), df_etn($keys)));
}, func_get_args());}

/**
 * 2016-06-30 «How to programmatically get a currency's name by its ISO code?» https://mage2.pro/t/1833
 * @used-by Df\Payment\ConfigProvider::config()
 * @used-by Dfe\AlphaCommerceHub\W\Event::currencyName()
 * @param string|C|string[]|C[]|null $c [optional]
 * @return string|string[]
 */
function df_currency_name($c = null) {/** @var string|string[] $r */
	if (is_array($c)) {
		$r = array_map(__FUNCTION__, $c);
	}
	else {
		static $rb; /** @var ResourceBundle $rb */
		$rb = $rb ?: (new CurrencyBundle)->get(df_locale())['Currencies'];
		$code = is_string($c) ? $c : df_currency_code($c); /** @var string $code */
		$r = $rb[$code][1] ?: $code;
	}
	return $r;
}

/**
 * 2019-09-04
 * @used-by vendor/inkifi/map/view/frontend/templates/create/form/buy.phtml
 * @return IPriceCurrency|PriceCurrency
 */
function df_price_currency() {return df_o(IPriceCurrency::class);}
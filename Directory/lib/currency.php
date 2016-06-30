<?php
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Store\Api\Data\StoreInterface;
/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string[]
 */
function df_currencies_codes_allowed($store = null) {
	return df_store($store)->getAvailableCurrencyCodes(true);
}

/**
 * 2016-06-30
 * «How to programmatically check whether a currency is allowed
 * and has an exchange rate to the base currency?» https://mage2.pro/t/1832
 * @param string $iso3
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string[]
 */
function df_currency_has_rate($iso3, $store = null) {return !!dfa(df_currencies_ctn($store), $iso3);}

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return array(string => string)
 */
function df_currencies_ctn($store = null) {
	$store = df_store($store);
	/** @var array(int => array(string => string)) */
	static $cache;
	/** @var string $cacheKey */
	$cacheKey = $store->getId();
	if (!isset($cache[$cacheKey])) {
		/** @var \Magento\Directory\Model\Currency $currency */
		$currency = df_o(\Magento\Directory\Model\Currency::class);
		/** @var string[] $codes */
		$codes = df_currencies_codes_allowed($store);
		// 2016-02-17
		// $rates ниже не содержит базовую валюту.
		/** @var string $baseCode */
		$baseCode = $store->getBaseCurrency()->getCode();
		/** @var array(string => float) $rates */
		$rates = $currency->getCurrencyRates($store->getBaseCurrency(), $codes);
		/** @var array(string => string) $result */
		$result = [];
		foreach ($codes as $code) {
			/** @var string $code */
			if ($baseCode === $code || isset($rates[$code])) {
				$result[$code] = df_currency_ctn($code);
			}
		}
		$cache[$cacheKey] = $result;
	}
	return $cache[$cacheKey];
}

/**
 * 2016-06-30
 * @param string $iso3
 * @return string
 */
function df_currency_ctn($iso3) {
	/** @var \ResourceBundle $cache */
	static $cache;
	if (!isset($cache))  {
		$cache = (new CurrencyBundle())->get(df_locale())['Currencies'];
	}
	return $cache[$iso3][1] ?: $iso3;
}

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return array(array(string => string))
 */
function df_currencies_options($store = null) {return df_map_to_options(df_currencies_ctn($store));}



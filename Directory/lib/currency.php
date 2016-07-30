<?php
use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
/**
 * 2016-07-04
 * «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * @param Currency|string|null $currency [optional]
 * @return Currency
 */
function df_currency($currency = null) {
	/** @var Currency $result */
	if (!$currency) {
		$result = df_currency_base();
	}
	else if ($currency instanceof Currency) {
		$result = $currency;
	}
	else {
		/** @var array(string => Currency) $cache */
		static $cache;
		if (!isset($cache[$currency])) {
			$cache[$currency] = df_create(Currency::class)->load($currency);
		}
		$result = $cache[$currency];
	}
	return $result;
}

/**
 * 2016-07-04
 * «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $scope [optional]
 * @return Currency
 */
function df_currency_base($scope = null) {
	/** @var string $code */
	$code = df_cfg(Currency::XML_PATH_CURRENCY_BASE, $scope);
	df_assert_string_not_empty($code);
	return df_currency($code);
}

/**
 * 2016-07-04
 * @param Currency|string|null $currency [optional]
 * @return string
 */
function df_currency_code($currency = null) {return df_currency($currency)->getCode();}

/**
 * 2016-07-04
 * «How to programmatically convert a money amount from a currency to another one?» https://mage2.pro/t/1842
 * @param float $amount
 * @param string|null $from [optional]
 * @param string|null $to [optional]
 * @return float
 */
function df_currency_convert($amount, $from = null, $to = null) {
	return df_currency($from)->convert($amount, df_currency_code($to));
}

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
		/** @var Currency $currency */
		$currency = df_o(Currency::class);
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
 * «How to programmatically get a currency's name by its ISO code?» https://mage2.pro/t/1833
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



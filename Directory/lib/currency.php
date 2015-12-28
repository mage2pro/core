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
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string[]
 */
function df_currency_rates($store = null) {
	return df_store($store)->getAvailableCurrencyCodes(true);
}

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
		/** @var array(string => float) $rates */
		$rates = $currency->getCurrencyRates($store->getBaseCurrency(), $codes);
		/** @var array(string => string) $result */
		$result = [];
		foreach ($codes as $code) {
			/** @var string $code */
			if (isset($rates[$code])) {
				/** @var mixed[] $allCurrencies */
				$allCurrencies = (new CurrencyBundle())->get(df_locale())['Currencies'];
				$result[$code] = $allCurrencies[$code][1] ?: $code;
			}
		}
		$cache[$cacheKey] = $result;
	}
	return $cache[$cacheKey];
}

/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return array(array(string => string))
 */
function df_currencies_options($store = null) {return df_map_to_options(df_currencies_ctn($store));}



<?php
use Magento\Store\Api\Data\StoreInterface;
/**
 * 2015-12-28
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string[]
 */
function df_country_codes_allowed($store = null) {
	return df_csv_parse(df_cfg('general/country/allow', $store));
}


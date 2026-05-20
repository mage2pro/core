<?php
use Magento\Framework\UrlInterface as U;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * 2026-05-20
 * 1) The result contains the trailing slash in either
 * @see \Magento\Framework\UrlInterface::URL_TYPE_LINK
 * and @see \Magento\Framework\UrlInterface::URL_TYPE_WEB
 * cases.
 * @used-by df_store_url_raw()
 * @used-by Df\Payment\Metadata::vars()
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_store_url($s = null, string $t = U::URL_TYPE_LINK):string {return df_store($s)->getBaseUrl(
	$t
);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_domain_current()
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_store_url_raw($s = null):string {return df_store_url($s, U::URL_TYPE_WEB);}
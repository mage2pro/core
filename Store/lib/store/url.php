<?php
use Df\Directory\Model\Country;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\UrlInterface as U;
use Magento\Sales\Model\Order as O;
use Magento\Store\Api\Data\StoreInterface as IStore;
use Magento\Store\Model\Information as Inf;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface as IStoreManager;
use Magento\Store\Model\StoreResolver;

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_store_url_link()
 * @used-by df_store_url_web()
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_store_url($s = null, string $t = U::URL_TYPE_LINK):string {return df_store($s)->getBaseUrl(
	$t
);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by Df\Payment\Metadata::vars()
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_store_url_link($s = null):string {return df_store_url($s, U::URL_TYPE_LINK);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_domain_current()
 * @param int|string|null|bool|IStore $s [optional]
 */
function df_store_url_web($s = null):string {return df_store_url($s, U::URL_TYPE_WEB);}
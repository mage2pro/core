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
 * 2016-01-11
 * @see df_store_codes()  
 * @see df_category_names()
 * @used-by Dfe\SalesSequence\Config\Next\Element::rows()
 * @return string[]
 */
function df_store_names(bool $withDefault = false, bool $codeKey = false):array {return array_map(
	function(IStore $store) {return $store->getName();}, df_stores($withDefault, $codeKey)
);}
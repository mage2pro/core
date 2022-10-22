<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite as StockIdForWebsite;
use Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface as IStockIdForWebsite;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;
/**
 * 2019-11-22
 * @used-by df_pt_has_qty()
 * @used-by df_qty()
 */
function df_msi():bool {return dfcf(function() {return df_module_enabled('Magento_Inventory');});}

/**
 * 2019-11-22
 * @used-by df_qty()
 * @uses df_msi_website2stockId()
 * @param P $p
 * @return int[]
 */
function df_msi_stock_ids(P $p):array {return array_filter(array_unique(array_map(
	'df_msi_website2stockId', $p->getWebsiteIds()
)));}

/**
 * 2019-11-22
 * 1) It returns null if the website is not linked to a stock.
 * 2) I use the @uses dfcf() caching because
 * @uses \Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite::execute()
 * makes a direct query to the database.
 * 3) The $v argument could be one of:
 * *) a website: W
 * *) a store: Store
 * *) a website's ID: int
 * *) a website's code: string
 * *) null or absert: the current website
 * *) true: the default website
 * @used-by df_msi_stock_ids()
 * @param W|Store|int|string|null|bool $v [optional]
 * @return int|null
 * @throws Exception
 * @throws NSE
 */
function df_msi_website2stockId($v = null) {return dfcf(function($c) {
	$i = df_o(StockIdForWebsite::class); /** @var IStockIdForWebsite|StockIdForWebsite $i */
	return $i->execute($c);
}, [df_website_code($v)]);}
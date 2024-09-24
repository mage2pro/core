<?php
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface as IDefaultStockProvider;
use Magento\InventoryCatalog\Model\DefaultStockProvider as DefaultStockProvider;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolver;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface as IStockIndexTableNameResolver;
/**
 * 2020-11-23
 * @used-by Df\InventoryCatalog\Plugin\Model\ResourceModel\AddStockDataToCollection::aroundExecute()
 * @return IDefaultStockProvider|DefaultStockProvider
 */
function df_default_stock_provider() {return df_o(IDefaultStockProvider::class);}

/**
 * 2020-11-23
 * @used-by Df\InventoryCatalog\Plugin\Model\ResourceModel\AddStockDataToCollection::aroundExecute()
 * @return IStockIndexTableNameResolver|StockIndexTableNameResolver
 */
function df_stock_index_table_name_resolver() {return df_o(IStockIndexTableNameResolver::class);}
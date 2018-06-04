<?php
use Magento\CatalogInventory\Api\StockRegistryInterface as IStockRegistry;
use Magento\CatalogInventory\Model\StockRegistry;
/**
 * 2018-06-04
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @return IStockRegistry|StockRegistry
 */
function df_stock_r() {return df_o(IStockRegistry::class);}
<?php
use Magento\Catalog\Model\Product as P;
use Magento\CatalogInventory\Api\Data\StockItemInterface as ISI;
use Magento\CatalogInventory\Api\StockRegistryInterface as IStockRegistry;
use Magento\CatalogInventory\Model\Stock\Item as SI;
use Magento\CatalogInventory\Model\StockRegistry;

/**
 * 2019-11-18
 * 1) It does not support the Multi Source Inventory:
 * https://devdocs.magento.com/guides/v2.3/inventory/index.html
 * https://devdocs.magento.com/guides/v2.3/inventory/catalog-inventory-replacements.html 
 * 2) It returns a float value, not an integer one.
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @used-by \Justuno\M2\Catalog\Variants::variant()
 * @param P|int $p
 * @return float
 */
function df_qty($p) {return df_stock($p)->getQty();}

/**
 * 2018-06-04
 * @used-by df_stock()
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @return IStockRegistry|StockRegistry
 */
function df_stock_r() {return df_o(IStockRegistry::class);}

/**
 * 2019-11-18
 * 1) It does not support the Multi Source Inventory:
 * https://devdocs.magento.com/guides/v2.3/inventory/index.html
 * https://devdocs.magento.com/guides/v2.3/inventory/catalog-inventory-replacements.html 
 * 2) @uses \Magento\CatalogInventory\Model\StockRegistry::getStockItem() supports the second argument: $scopeId.
 * Magento 2 core modules pass a website ID there.
 * But actually the argument is ignored:
 *		public function getStockItem($productId, $scopeId = null) {
 *			$scopeId = $this->stockConfiguration->getDefaultScopeId();
 *			return $this->stockRegistryProvider->getStockItem($productId, $scopeId);
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/CatalogInventory/Model/StockRegistry.php#L80-L89
 * @used-by df_qty()
 * @param P|int $p
 * @return ISI|SI
 */
function df_stock($p) {return df_stock_r()->getStockItem(df_product_id($p));}
<?php
use Magento\Catalog\Model\Product as P;
use Magento\CatalogInventory\Api\StockRegistryInterface as IStockRegistry;
use Magento\CatalogInventory\Helper\Stock as StockH;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\InventorySales\Model\GetProductSalableQty as Qty;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface as IQty;

/**
 * 2019-11-18 It returns a float value, not an integer one.
 * @used-by Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @param P|int $p
 */
function df_qty($p):float {
	df_assert_qty_supported($p);
	# 2019-11-21 https://devdocs.magento.com/guides/v2.3/inventory/reservations.html#checkout-services
	if (!df_msi()) {
		$r = df_stock_r()->getStockItem(df_product_id($p))->getQty();
	}
	else {
		$i = df_o(IQty::class); /** @var IQty|Qty $i */
		$sku = $p->getSku(); /** @var string $sku */
		$r = array_sum(df_map(df_msi_stock_ids($p), function(int $sid) use($i, $sku):float {return $i->execute(
			$sku, $sid
		);}));
	}
	return $r;
}

/**
 * 2020-06-05
 * @used-by BlushMe\Checkout\Block\Extra::items()
 */
function df_stock_h():StockH {return df_o(StockH::class);}

/**
 * 2018-06-04
 * @used-by df_qty()
 * @used-by Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 * @return IStockRegistry|StockRegistry
 */
function df_stock_r() {return df_o(IStockRegistry::class);}
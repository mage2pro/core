<?php
use Df\Core\Exception as DFE;
use Magento\Catalog\Model\Product as P;
use Magento\CatalogInventory\Api\StockConfigurationInterface as ICfg;
use Magento\CatalogInventory\Model\Configuration as Cfg;
use Magento\InventoryConfiguration\Model\IsSourceItemManagementAllowedForProductType as AllowedForPT;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface as IAllowedForPT;
/**
 * 2019-11-22
 * @used-by df_qty()
 * @param P $p
 * @throws DFE
 */
function df_assert_qty_supported(P $p) {
	$t = $p->getTypeId(); /** @var string $t */
	df_assert(df_msi() ? df_msi_allowed_for_pt()->execute($t) : df_stock_cfg()->isQty($t),
		"Products of type `$t` do not have a quantity."
	);
}

/**
 * 2019-11-21
 *	{
 *		"bundle": false,
 *		"configurable": false,
 *		"downloadable": true,
 *		"grouped": false,
 *		"simple": true,
 *		"virtual": true
 *	}
 * @used-by df_assert_qty_supported()
 * @return IAllowedForPT|AllowedForPT
 */
function df_msi_allowed_for_pt() {return df_o(IAllowedForPT::class);}

/**
 * 2019-11-22
 * @used-by df_assert_qty_supported()
 * @return ICfg|Cfg
 */
function df_stock_cfg() {return df_o(ICfg::class);}
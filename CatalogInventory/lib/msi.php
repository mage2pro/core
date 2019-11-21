<?php
use Magento\InventoryConfiguration\Model\IsSourceItemManagementAllowedForProductType as AllowedForPT;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface as IAllowedForPT;
use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface as IGetProductSalableQty;
/**
 * 2019-11-21
 * @return IAllowedForPT|AllowedForPT
 */
function df_msi_allowed_for_pt() {return df_o(IAllowedForPT::class);}

/**
 * 2019-11-21
 * @return IGetProductSalableQty|GetProductSalableQty
 */
function df_msi_salable_qty() {return df_o(IGetProductSalableQty::class);}
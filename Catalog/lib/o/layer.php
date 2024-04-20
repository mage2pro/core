<?php
use Magento\Catalog\Model\Layer as L;
use Magento\Catalog\Model\Layer\Resolver as LR;

/**
 * 2024-04-20
 * 1) "Implement `df_catalog_layer()`": https://github.com/mage2pro/core/issues/364
 * 2) https://stackoverflow.com/a/46414822
 * 3) @see \Magento\Catalog\Model\Layer exists since Magento 2.0.0: https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Layer.php
 * 4) @see \Magento\Catalog\Model\Layer\Resolver exists since Magento 2.0.0: https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Layer/Resolver.php
 * 5) @see df_catalog_locator()
 * @used-by df_category_current()
 */
function df_catalog_layer():L {
	$lr = df_o(LR::class); /** @var LR $lr */
	return $lr->get();
}
<?php
use Magento\Catalog\Model\Layer as L;
use Magento\Catalog\Model\Layer\Resolver as LR;

/**
 * 2024-04-20
 * 1) "Implement `df_catalog_layer()`": https://github.com/mage2pro/core/issues/364
 * 2) https://stackoverflow.com/a/46414822=
 */
function df_catalog_layer():L {
	$lr = df_o(LR::class); /** @var LR $lr */
	return $lr->get();
}
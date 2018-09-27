<?php
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product as P;

/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @used-by df_product_current()
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 */
function df_catalog_locator() {return df_o(LocatorInterface::class);}

/**
 * 2018-09-27
 * @param \Closure|bool|mixed $onError
 * @return P|null
 * @throws \Exception
 */
function df_product_current($onError = null) {return df_try(function() {return
	df_catalog_locator()->getProduct()
;}, $onError);}
<?php
use Df\Core\Exception as DFE;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product as P;

/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @used-by df_product_current()
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 * @throws DFE
 */
function df_catalog_locator() {
	df_assert(df_is_backend()); // 2019-08-01 Locator is available only in backend.
	return df_o(LocatorInterface::class);
}

/**
 * 2018-09-27
 * @param \Closure|bool|mixed $onError
 * @return P|null
 * @throws \Exception
 */
function df_product_current($onError = null) {return df_try(function() {return
	df_catalog_locator()->getProduct()
;}, $onError);}
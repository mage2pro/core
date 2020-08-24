<?php
use Df\Core\Exception as DFE;
use Magento\Catalog\Model\Locator\LocatorInterface as ILocator;
use Magento\Catalog\Model\Locator\RegistryLocator;
/**
 * 2016-02-25 https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @used-by df_product_current()
 * @return ILocator|RegistryLocator
 * @throws DFE
 */
function df_catalog_locator() {
	df_assert(df_is_backend()); # 2019-08-01 Locator is available only in backend.
	return df_o(ILocator::class);
}
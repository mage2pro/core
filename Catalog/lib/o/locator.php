<?php
use Df\Core\Exception as DFE;
use Magento\Catalog\Model\Locator\LocatorInterface as ILocator;
use Magento\Catalog\Model\Locator\RegistryLocator;

/**
 * 2016-02-25 https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10
 * 2019-08-01 `Magento\Catalog\Model\Locator\LocatorInterface` is available only in the backend.
 * 2024-04-15
 * 1) https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10
 * 2) `Magento\Catalog\Model\Locator\LocatorInterface` is absent in Magento < 2.1:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Locator/LocatorInterface.php
 * @used-by df_product_current()
 * @return ILocator|RegistryLocator
 * @throws DFE
 */
function df_catalog_locator() {
	df_assert(df_is_backend());
	return df_o(ILocator::class);
}

/**
 * 2024-04-15
 * `Magento\Catalog\Model\Locator\LocatorInterface` is absent in Magento < 2.1:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Locator/LocatorInterface.php
 * @used-by df_product_current()
 */
function df_catalog_locator_exists():bool {return df_class_exists(ILocator::class);}
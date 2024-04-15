<?php
use Df\Core\Exception as DFE;
use Magento\Catalog\Helper\Data as H;
use Magento\Catalog\Helper\Output as OutputH;
use Magento\Catalog\Model\Locator\LocatorInterface as ILocator;
use Magento\Catalog\Model\Locator\RegistryLocator;
/**
 * 2021-12-21 @deprecated It is unused.
 */
function df_catalog_h():H {return df_o(H::class);}

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
 * 2020-10-30
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/tabs/overview.phtml (https://github.com/cabinetsbay/site/issues/105)
 * @used-by app/design/frontend/TradeFurnitureCompany/default/Magento_Catalog/templates/category/description.phtml
 */
function df_catalog_output():OutputH {return df_o(OutputH::class);}
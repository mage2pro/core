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
 * 2016-02-25 https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @used-by df_product_current()
 * @return ILocator|RegistryLocator
 * @throws DFE
 */
function df_catalog_locator() {
	df_assert(df_is_backend()); # 2019-08-01 Locator is available only in backend.
	return df_o(ILocator::class);
}

/**
 * 2020-10-30
 * @used-by app/design/frontend/TradeFurnitureCompany/default/Magento_Catalog/templates/category/description.phtml
 */
function df_catalog_output():OutputH {return df_o(OutputH::class);}
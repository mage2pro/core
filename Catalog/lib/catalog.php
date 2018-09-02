<?php
use Magento\Catalog\Model\Locator\LocatorInterface;

/**
 * 2016-02-25
 * https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * @return LocatorInterface|\Magento\Catalog\Model\Locator\RegistryLocator
 */
function df_catalog_locator() {return df_o(LocatorInterface::class);}
<?php
use Magento\CatalogImportExport\Model\Import\Product\StoreResolver;
/**
 * 2019-09-20
 * @used-by Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 */
function df_ie_store_r():StoreResolver {return df_o(StoreResolver::class);}
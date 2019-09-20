<?php
use Magento\CatalogImportExport\Model\Import\Product\StoreResolver;
/**
 * 2019-09-20
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 * @return StoreResolver
 */
function df_ie_store_r() {return df_o(StoreResolver::class);}
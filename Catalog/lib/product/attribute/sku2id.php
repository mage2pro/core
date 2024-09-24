<?php
/**
 * 2019-09-22
 * @see df_att_code2id()
 * @used-by Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 */
function df_product_sku2id(string $sku):int {return (int)df_product_res()->getIdBySku($sku);}
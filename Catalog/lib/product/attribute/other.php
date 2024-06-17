<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2021-04-24
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 */
function df_product_att_changed(P $p, string $k):bool {return $p->getStoreId() ? !is_null($p[$k]) : $p->dataHasChangedFor($k);}

/**              
 * 2019-09-22
 * @used-by \Dfe\Color\Observer\ProductImportBunchSaveAfter::execute()
 */
function df_product_sku2id(string $sku):int {return (int)df_product_res()->getIdBySku($sku);}

/**
 * 2020-01-31
 * @see \Magento\Catalog\Model\Product::getAttributeText()
 * @uses \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::getOptionText()
 * @used-by \Df\Catalog\Test\product\attribute::df_product_att_val()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @throws NSE
 */
function df_product_att_val(P $p, string $c, string $d = ''):string {return df_att_val($p, df_product_att($c), $d);}
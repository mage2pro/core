<?php
use Closure as F;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Attribute\Repository as R;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as A;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2019-08-21
 * @used-by df_product_att()
 */
function df_product_atts_r():R {return df_o(R::class);}

/**
 * 2019-08-21                   
 * @used-by df_product_att_options()
 * @used-by df_product_att_val()
 * @param F|bool|mixed $onE [optional]
 * @return A|null
 * @throws NSE
 */
function df_product_att(string $c, $onE = true) {return df_try(function() use($c) {return df_product_atts_r()->get($c);}, $onE);}

/**
 * 2021-04-24
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 */
function df_product_att_changed(P $p, string $k):bool {return $p->getStoreId() ? !is_null($p[$k]) : $p->dataHasChangedFor($k);}

/**      
 * 2019-10-22
 * @used-by df_product_att_options_m()
 * @used-by \Dfe\Color\Image::opts()
 * @return array(array(string => int|string))
 */
function df_product_att_options(string $c):array {return dfcf(function($c) {return
	df_product_att($c)->getSource()->getAllOptions(false)
;}, [$c]);}

/**
 * 2019-10-22
 * @used-by \Dfe\Color\Image::opts()
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Block\Adminhtml\Product\Grid::aroundAddColumn()
 * @return array(array(string => int|string))
 */
function df_product_att_options_m(string $c):array {return df_options_to_map(df_product_att_options($c));}

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
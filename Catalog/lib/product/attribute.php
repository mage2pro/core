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
 * @used-by df_product_att_val_s()
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
 * @used-by \Df\Catalog\Test\product\attribute::df_product_att_val_s()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param P $p
 * @param string $c
 * @param F|bool|mixed $onE [optional]
 * @return string|null
 * @throws NSE
 */
function df_product_att_val_s(P $p, $c, $onE = true) {return df_try(function() use($p, $c) {
	/** @var string|false|string[] $r */ /** @var string|int $v */
	$r = df_product_att($c)->getSource()->getOptionText($v = $p[$c]);
	/**
	 * 2020-01-31
	 * @see \Magento\Eav\Model\Entity\Attribute\Source\Table::getOptionText() can return an empty array
	 * for an attribute's value (e.g., for the `description` attribute), if the value contains a comma.
	 */
	return false === $r || is_array($r) ? $v : $r;
}, $onE);}
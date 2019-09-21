<?php
use Magento\Catalog\Model\Product\Attribute\Repository as R;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as A;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2019-08-21
 * @used-by df_product_att()
 * @return R
 */
function df_product_atts_r() {return df_o(R::class);}

/**
 * 2019-08-21
 * @used-by \Dfe\Color\Image::opts()
 * @param string $code
 * @return A
 * @throws NSE
 */
function df_product_att($code) {return df_product_atts_r()->get($code);}

/**              
 * 2019-09-22
 * @param string $sku
 * @return int
 */
function df_product_sku2id($sku) {return (int)df_product_res()->getIdBySku($sku);}
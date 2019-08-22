<?php
use Magento\Catalog\Model\Product\Attribute\Repository as R;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as A;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
/**
 * 2019-08-21
 * @return R
 */
function df_product_atts_r() {return df_o(R::class);}

/**
 * 2019-08-21
 * @param string $code
 * @return A
 * @throws NSE
 */
function df_product_att($code) {return df_product_atts_r()->get($code);}
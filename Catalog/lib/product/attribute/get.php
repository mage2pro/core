<?php
use Closure as F;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as A;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2019-08-21
 * @used-by df_product_att_options()
 * @used-by df_product_att_val()
 * @param F|bool|mixed $onE [optional]
 * @throws NSE
 */
function df_product_att(string $c, $onE = true):?A {return df_try(
	function() use($c):?A {return df_product_atts_r()->get($c);}, $onE
);}
<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2020-01-31
 * @see df_product_att_html()
 * @see \Magento\Catalog\Model\Product::getAttributeText()
 * @uses \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::getOptionText()
 * @used-by \Df\Catalog\Test\product\attribute::df_product_att_val()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @throws NSE
 */
function df_product_att_val(P $p, string $c, string $d = ''):string {return df_att_val($p, df_product_att($c), $d);}
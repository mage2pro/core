<?php
use Magento\Catalog\Model\Product as P;

/**
 * 2018-06-04
 * @see df_product()
 * @used-by \Frugue\Configurable\Plugin\ConfigurableProduct\Helper\Data::aroundGetOptions()
 */
function df_product_load(int $id):P {return df_product_r()->getById($id, false, null, true);}
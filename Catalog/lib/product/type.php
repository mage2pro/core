<?php
use Magento\Bundle\Model\Product\Type as B;
use Magento\Catalog\Model\Product as P;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as C;
use Magento\GroupedProduct\Model\Product\Type\Grouped as G;

/**
 * 2021-04-24
 * @see df_configurable()
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @param P $p
 */
function df_product_is_bundle(P $p):bool {return B::TYPE_CODE === $p->getTypeId();}

/**
 * 2017-04-20
 * @see df_configurable()
 * @see df_not_configurable()
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @param string $t
 */
function df_product_type_composite($t):bool {return in_array($t, [B::TYPE_CODE, C::TYPE_CODE, G::TYPE_CODE]);}
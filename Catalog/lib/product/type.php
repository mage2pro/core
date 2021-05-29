<?php
use Magento\Bundle\Model\Product\Type as B;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Type as T;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as C;
use Magento\Downloadable\Model\Product\Type as D;
use Magento\GroupedProduct\Model\Product\Type\Grouped as G;

/**
 * 2021-04-24
 * @see df_configurable()
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @param P $p
 * @return bool
 */
function df_product_is_bundle(P $p) {return B::TYPE_CODE === $p->getTypeId();}

/**
 * 2017-04-20
 * @see df_configurable()
 * @see df_not_configurable()
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @param string $t
 * @return bool
 */
function df_product_type_composite($t) {return in_array($t, [B::TYPE_CODE, C::TYPE_CODE, G::TYPE_CODE]);}

/**
 * 2015-11-14
 * @used-by \Dfe\Frontend\ConfigSource\Visibility\Product\VD::needHideFor()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::tangible()
 * @param P $p
 * @return bool
 */
function df_tangible(P $p) {return !in_array($p->getTypeId(), [D::TYPE_DOWNLOADABLE, T::TYPE_VIRTUAL, 'gifcard']);}
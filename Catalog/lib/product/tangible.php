<?php
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Type as T;
use Magento\Downloadable\Model\Product\Type as D;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2021-05-31
 * @param P|O|Q $v
 * @return bool
 */
function df_intangible($v) {return $v instanceof P ? !df_tangible($v) : !df_find(
	df_oqi_roots(df_assert_oq($v)), function($i) {/** @var QI|OI $i */return df_tangible($i->getProduct());}
);}

/**
 * 2015-11-14
 * @used-by df_intangible()
 * @used-by \Dfe\Frontend\ConfigSource\Visibility\Product\VD::needHideFor()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::tangible()
 * @param P $p
 * @return bool
 */
function df_tangible(P $p) {return !in_array($p->getTypeId(), [D::TYPE_DOWNLOADABLE, T::TYPE_VIRTUAL, 'gifcard']);}
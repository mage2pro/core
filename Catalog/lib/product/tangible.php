<?php
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote as Q;

/**
 * 2015-11-14
 * 2021-06-02
 * 1) The previous implementation: `!in_array($p->getTypeId(), [D::TYPE_DOWNLOADABLE, T::TYPE_VIRTUAL, 'gifcard'])`
 * https://github.com/mage2pro/core/blob/7.5.2/Catalog/lib/product/tangible.php#L19-L27
 * 2) @see \Magento\Downloadable\Model\Product\Type inherits from
 * @see \Magento\Catalog\Model\Product\Type\Virtual
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Downloadable/Model/Product/Type.php#L17
 * 3) «gifcard» is not a built-in product type.
 * 2021-06-03
 * The previous `df_intangible()` implementation:
 *		return $v instanceof P ? !df_tangible($v) : !df_find(
 *			df_oqi_roots(df_assert_oq($v)), function($i) {return df_tangible($i->getProduct());}
 *		)
 * https://github.com/mage2pro/core/blob/7.5.2/Catalog/lib/product/tangible.php#L10-L17
 * @used-by \Amasty\Checkout\Model\QuoteManagement::saveInsertedInfo()
 * @used-by \Dfe\Frontend\ConfigSource\Visibility\Product\VD::needHideFor()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::tangible()
 * @used-by app/code/Interactivated/Quotecheckout/view/frontend/templates/dashboard/onepage/billing.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/128)
 * @uses \Magento\Catalog\Model\Product::isVirtual()
 * @uses \Magento\Quote\Model\Quote::isVirtual()
 * @param P|Q|null $v [optional]
 */
function df_tangible($v = null):bool {$v = $v ?: df_quote(); return !$v->isVirtual();}
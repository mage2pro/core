<?php
use Magento\Catalog\Model\Product as P;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as T;

/**
 * 2016-05-01 How to programmatically detect whether a product is configurable? https://mage2.pro/t/1501
 * @see df_product_is_bundle()
 * @see df_product_type_composite()
 * @used-by df_not_configurable()
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L26
 */
function df_configurable(P $p):bool {return T::TYPE_CODE === $p->getTypeId();}

/**
 * 2019-09-18
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L27
 * @return P[]
 */
function df_configurable_children(P $p):array {
	df_assert(df_configurable($p));
	$t = $p->getTypeInstance(); /** @var T $t */
	return $t->getUsedProducts($p);
}

/**
 * 2018-09-02
 * @used-by df_wishlist_item_candidates()
 * @param P[] $pp
 * @return P[]
 */
function df_not_configurable(array $pp):array {return array_filter($pp, function(P $p) {return !df_configurable($p);});}
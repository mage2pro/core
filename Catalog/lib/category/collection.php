<?php
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CC;
/**
 * 2021-11-30
 * 2024-05-07 "Improve `df_category_c()`": https://github.com/mage2pro/core/issues/366
 * @see df_pc()
 * @see df_product_c()
 * @used-by df_category_children()
 * @param int[]|int|string|null $ids [optional]
 */
function df_category_c($ids = null, bool $activeOnly = true):CC {
	$r = df_new_om(CC::class); /** @var CC $r */
	if (null !== $ids) {
		$r->addIdFilter($ids);
	}
	if ($activeOnly) {
		$r->addIsActiveFilter();
	}
	return $r;
}

/**
 * 2021-11-30
 * https://github.com/JustunoCom/m2/blob/1.7.3/Controller/Response/Catalog.php#L97
 * @noinspection PhpParamsInspection
 * @used-by df_category_children_map()
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/l2/l3.phtml (https://github.com/cabinetsbay/site/issues/112)
 * @param C|int $c
 * @param string|string[] $a [optional]
 */
function df_category_children($c, $a = '*'):CC {return
	df_category_c()->addIsActiveFilter()->addIdFilter(df_category($c)->getChildren())->addAttributeToSelect($a)
;}

/**
 * 2021-11-30
 * @used-by \TFC\GoogleShopping\Att\Brand::v() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/8)
 * @param C|int $c
 * @return array(int => string)
 */
function df_category_children_map($c):array {return dfcf(function(C $c) {return df_map_r(
	df_category_children($c, 'name'), function(C $c) {return [$c->getId(), $c->getName()];}
);}, [df_category($c)]);}
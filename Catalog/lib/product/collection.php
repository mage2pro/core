<?php
use Closure as F;
use Df\Catalog\Plugin\Model\Indexer\Product\Flat\State as PFlat;
use Df\InventoryCatalog\Plugin\Model\ResourceModel\AddStockDataToCollection as PAddStock;
use Magento\Catalog\Model\ResourceModel\Product\Collection as C;

/**
 * 2019-09-18
 * @see df_category_c()
 * @used-by df_product_c()
 * @used-by https://github.com/tradefurniturecompany/report/blob/1.0.3/view/frontend/templates/index.phtml#L17
 * @used-by \TFC\Image\Command\C3::pc()
 * @used-by \TFC\Image\Command\C3::pcL()
 */
function df_pc():C {return df_new_om(C::class);}

/**
 * 2020-11-24
 * @see df_pc_preserve_absent()
 * @see df_pc_preserve_absent_f()
 * 1) "Add an ability to temporary disable the flat mode for products": https://github.com/mage2pro/core/issues/149
 * 2) Currently, it is unused here, but used in Justuno: https://github.com/justuno-com/m2/issues/23
 * @return mixed|null
 */
function df_pc_disable_flat(F $f = null) {
	if (!$f) {
		PFlat::$DISABLE = true;
		$r = null;
	}
	else {
		try {
			$prev = PFlat::$DISABLE;
			PFlat::$DISABLE = true;
			$r = $f(); /** @var mixed $r */
		}
		finally {PFlat::$DISABLE = $prev;}
	}
	return $r;
}

/**
 * 2020-11-23
 * @see df_pc_preserve_absent_f()
 * 1) "Add an ability to preserve out of stock (including just disabled) products in a collection
 * despite of the `cataloginventory/options/show_out_of_stock` option's value": https://github.com/mage2pro/core/issues/148
 * 2) Currently, it is unused here, but used in Justuno:
 * https://github.com/justuno-com/m2/issues/19
 * https://github.com/justuno-com/m2/issues/22
 * 2020-11-24
 * The solution works only if the «Use Flat Catalog Product» option is disabled.
 * @see df_pc_disable_flat()
 * If the the «Use Flat Catalog Product» option is enabled,
 * then the products collection is loaded directly from a `catalog_product_flat_<store>` table,
 * and such tables do not contain disabled products at least in Magento 2.4.0.
 * 2022-11-21 @deprecated It is unused.
 */
function df_pc_preserve_absent(C $c):C {return $c->setFlag(PAddStock::PRESERVE_ABSENT, true);}

/**
 * 2020-11-23
 * 1) "Add an ability to preserve out of stock (including just disabled) products in a collection
 * despite of the `cataloginventory/options/show_out_of_stock` option's value": https://github.com/mage2pro/core/issues/148
 * 2) @see df_pc_preserve_absent() affects only a single explicitly accessible collection.
 * Sometimes it is not enough:
 * e.g. when we call @see \Magento\ConfigurableProduct\Model\Product\Type\Configurable::getUsedProducts()
 * the children collection is created internally (implicitly),
 * so we can not call @see df_pc_preserve_absent() for it before it is loaded.
 * 3) Currently, it is unused here, but used in Justuno:
 * https://github.com/justuno-com/m2/issues/19
 * https://github.com/justuno-com/m2/issues/22
 * 2020-11-24
 * The solution works only if the «Use Flat Catalog Product» option is disabled.
 * @see df_pc_disable_flat()
 * If the the «Use Flat Catalog Product» option is enabled,
 * then the products collection is loaded directly from a `catalog_product_flat_<store>` table,
 * and such tables do not contain disabled products at least in Magento 2.4.0.
 * 2022-10-24
 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
 * @return mixed
 */
function df_pc_preserve_absent_f(F $f) {
	try {
		$prev = PAddStock::$PRESERVE_ABSENT_F;
		PAddStock::$PRESERVE_ABSENT_F = true;
		$r = $f(); /** @var mixed $r */
	}
	finally {PAddStock::$PRESERVE_ABSENT_F = $prev;}
	return $r;
}

/**
 * 2019-09-18
 * 2020-11-23 @deprecated
 * @see df_category_c()
 * @used-by \BlushMe\Checkout\Block\Extra::items()
 */
function df_product_c():C {return df_pc();}
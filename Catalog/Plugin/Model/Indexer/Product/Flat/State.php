<?php
namespace Df\Catalog\Plugin\Model\Indexer\Product\Flat;
use Closure as F;
use Magento\Catalog\Model\Indexer\Product\Flat\State as Sb;
# 2020-11-24 "Add an ability to temporary disable the flat mode for products": https://github.com/mage2pro/core/issues/149
final class State {
	/**
	 * 2020-11-24
	 * @see \Magento\Catalog\Model\Indexer\AbstractFlatState::isFlatEnabled()
	 * @param Sb $sb
	 * @param F $f
	 */
	function aroundIsFlatEnabled(Sb $sb, F $f):bool {return !self::$DISABLE && $f();}

	/**
	 * 2020-11-24
	 * @used-by df_pc_disable_flat()
	 * @used-by self::aroundIsFlatEnabled()
	 * @var bool
	 */
	static $DISABLE;
}
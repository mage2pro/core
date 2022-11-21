<?php
namespace Df\Catalog\Test\product;
use Magento\Catalog\Model\Product as P;
# 2020-01-31
class attribute extends \Df\Core\TestCase {
	/** 2020-01-31 @test */
	function t00():void {}

	/** 2020-01-31 @test */
	function df_product_att_val_s():void {
		$p = df_product(45); /** @var P $p */
		echo df_product_att_val_s($p, 'description');
		echo df_product_att_val_s($p, 'color');
	}
}
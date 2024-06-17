<?php
/**
 * 2019-10-22
 * @used-by df_product_att_options_m()
 * @used-by \Dfe\Color\Image::opts()
 * @return array(array(string => int|string))
 */
function df_product_att_options(string $c):array {return dfcf(function($c) {return
	df_product_att($c)->getSource()->getAllOptions(false)
;}, [$c]);}

/**
 * 2019-10-22
 * @used-by \Dfe\Color\Image::opts()
 * @used-by \PPCs\Core\Plugin\Iksanika\Stockmanage\Block\Adminhtml\Product\Grid::aroundAddColumn()
 * @return array(array(string => int|string))
 */
function df_product_att_options_m(string $c):array {return df_options_to_map(df_product_att_options($c));}
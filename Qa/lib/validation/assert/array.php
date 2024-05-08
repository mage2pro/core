<?php
use Df\Core\Exception as DFE;

/**
 * @used-by df_wishlist_item_candidates()
 * @used-by \Dfe\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\Xml\X::importArray()
 * @used-by \Dfe\FacebookLogin\Customer::responseJson()
 */
function df_assert_array(array $a):array {return $a;}

/**
 * 2017-02-18
 * @used-by df_clean_keys()
 * @return array(string => mixed)
 * @throws DFE
 */
function df_assert_assoc(array $a):array {return df_is_assoc($a) ? $a : df_error('The array should be associative.');}

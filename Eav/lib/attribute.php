<?php
use Closure as F;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
/**
 * 2019-06-15
 * @used-by df_customer_att_pos_set()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @see df_product_attrs_r()
 */
function df_att_code2id(string $c):int {return df_first(df_fetch_col_int(
	'eav_attribute', 'attribute_id', 'attribute_code', $c
));}

/**
 * 2024-05-16 "Implement `df_att_val_s()`": https://github.com/mage2pro/core/issues/373
 * @param F|bool|mixed $onE [optional]
 */
function df_att_val(A $a, string $k, $onE = true):string {

}
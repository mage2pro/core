<?php
/**
 * 2019-06-15
 * @used-by df_customer_att_pos_set()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @see df_product_attrs_r()
 * @param string $c
 */
function df_att_code2id($c):int {return df_first(df_fetch_col_int('eav_attribute', 'attribute_id', 'attribute_code', $c));}
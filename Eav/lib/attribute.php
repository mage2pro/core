<?php
/**
 * 2019-06-15
 * @used-by df_customer_att_pos_set()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @param string $c
 * @return int
 */
function df_att_code2id($c) {return df_fetch_col_int(
	'eav_attribute', 'attribute_id', 'attribute_code', $c
);}
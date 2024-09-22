<?php
/**
 * 2019-06-13
 * @see df_kv()
 * @used-by \KingPalm\B2B\Observer\RegisterSuccess::execute()
 * @param array(string => string) $a
 */
function df_kv_table(array $a):string {return df_tag('table', [], df_map_k(
	df_clean($a), function(string $k, $v):string {return
		df_tag('tr', [], [
			df_tag('td', [], $k)
			,df_tag('td', [], df_dump($v))
		])
	;}
));}
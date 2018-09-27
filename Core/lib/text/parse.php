<?php
/**
 * 2018-09-27
 * @param string $s
 * @return string|string[]
 */
function df_parse_colon($s) {return df_map_r(df_explode_n($s), function($s) {return
	df_trim(explode(':', $s));
});}
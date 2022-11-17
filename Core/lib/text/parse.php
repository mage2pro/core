<?php
/**
 * 2018-09-27
 * @used-by \Dfe\TBCBank\API\Client::_construct()
 * @used-by \Dfe\TBCBank\Test\CaseT\CheckResult::t01()
 * @return string|string[]
 */
function df_parse_colon(string $s) {return df_map_r(df_explode_n($s), function($s) {return df_trim(explode(':', $s));});}
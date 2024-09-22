<?php
/**
 * 2017-07-09
 * @see df_dump()
 * @see df_kv_table()
 * @used-by df_api_rr_failed()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\Qa\Failure\Error::preface()
 * @used-by \Df\Sentry\Client::send_http()
 * @param array(string => string) $a
 */
function df_kv(array $a, int $pad = 0):string {return df_cc_n(df_map_k(df_clean($a), function($k, $v) use($pad) {return
	(!$pad ? "$k: " : df_pad("$k:", $pad)) . (df_is_stringable($v) ? $v : df_json_encode($v))
;}));}
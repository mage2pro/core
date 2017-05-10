<?php
/**
 * 2017-05-10 $_SERVER['HOME'] is defined only in the CLI mode.
 * @used-by df_github_token()
 * @param string|null $k [optional]
 * @return string|null|array(string => mixed)
 */
function df_credentials($k = null) {return dfak(dfcf(function() {return df_json_decode(file_get_contents(
	(df_my_local() ? 'C:/tools/shell/home' : '/var/www') . '/.credentials/credentials.json'
));}), $k);}
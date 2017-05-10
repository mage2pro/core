<?php
/**
 * 2017-05-10
 * @used-by df_github_key()
 * @param string|null $k [optional]
 * @return string|null|array(string => mixed)
 */
function df_credentials($k = null) {return dfak(dfcf(function() {return df_json_decode(file_get_contents(
	$_SERVER['HOME'] . '/.credentials/credentials.json'
));}), $k);}
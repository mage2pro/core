<?php
/**
 * 2017-05-10 $_SERVER['HOME'] is defined only in the CLI mode.
 * @used-by df_github_token()
 * @return string|null|array(string => mixed)
 */
function df_credentials(string $k = '') {return dfac(function() {return df_json_file_read(
	(df_my_local() ? 'C:/tools/shell/home' : '/var/www') . '/.credentials/credentials.json'
);}, $k);}
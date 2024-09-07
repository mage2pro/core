<?php
use Closure as F;

/**
 * 2020-01-29
 * @see dfaoc()
 * @used-by df_credentials()
 * @used-by dfe_portal_module()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\OAuth\App::state()
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfac(F $f, $k = '', $d = null) {return dfa(dfcf($f, [], [], false, 1), $k, $d);}
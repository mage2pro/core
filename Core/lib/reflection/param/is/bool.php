<?php
use ReflectionParameter as P;

/**
 * 2024-09-21 https://www.php.net/manual/en/reflectionnamedtype.getname.php#128874
 * @used-by df_call()
 */
function dfr_param_is_bool(P $p):bool {return 'bool' === dfr_param_type($p);}
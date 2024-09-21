<?php
use SimpleXMLElement as CX;

/**
 * @deprecated It is unused.
 * @param bool|callable $d [optional]
 */
function df_leaf_b(CX $e = null, $d = false):bool {return df_bool(df_leaf($e, $d));}
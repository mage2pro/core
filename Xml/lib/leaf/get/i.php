<?php
use SimpleXMLElement as CX;

/**
 * 2015-08-16 Намеренно убрал параметр $default.
 * 2022-11-15 @deprecated It is unused.
 */
function df_leaf_i(CX $e = null):int {return df_int(df_leaf($e));}
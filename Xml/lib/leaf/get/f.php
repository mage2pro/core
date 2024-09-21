<?php
use Df\Xml\X;
use SimpleXMLElement as CX;
/**
 * 2015-08-16 Намеренно убрал параметр $default.
 * 2022-11-15 @deprecated It is unused.
 */
function df_leaf_f(CX $e = null):float {return df_float(df_leaf($e));}
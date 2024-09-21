<?php
use SimpleXMLElement as CX;

/**
 * 2022-11-15 @deprecated It is unused.
 * @param string|mixed|null|callable $d [optional]
 * @return string|mixed|null
 */
function df_leaf_child(CX $e, string $child, $d = null) {return df_leaf($e->{$child}, $d);}
<?php
/**
 * 2024-09-22
 * @see df_is_stringable()
 */
function df_is_complex($v):bool {return is_array($v) || is_object($v);}
<?php
use Df\Core\Exception as DFE;
/**
 * 2024-09-22
 * @param string|string[]|array(string => mixed)|mixed|T|null ...$a
 * @return mixed
 * @throws DFE
 */
function df_assert_stringable($v, ...$a) {return df_assert(df_is_stringable($v), ...$a);}
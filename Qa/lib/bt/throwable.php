<?php
use Throwable as T;

/**
 * 2023-08-25 "The exception point is not included to the exception's trace": https://github.com/mage2pro/core/issues/334
 * @used-by df_bt()
 * @used-by \Df\Qa\Failure\Exception::trace()
 */
function df_bt_th(T $t) {return array_merge([['file' => $t->getFile(), 'line' => $t->getLine()]], $t->getTrace());}
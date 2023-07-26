<?php
/**
 * 2023-07-26
 * @used-by df_caller_m()
 * @used-by df_caller_module()
 */
function df_bt_entry_is_method(array $e):bool {return dfa_has_keys($e, ['class', 'function']);}

/**
 * 2023-07-26
 * @used-by df_caller_module()
 */
function df_bt_entry_is_phtml(array $e):bool {return df_ends_with(dfa($e, 'file'), '.phtml');}
<?php
/**
 * 2023-07-26 "Implement `df_bt_entry_file()`": https://github.com/mage2pro/core/issues/279
 * @used-by df_log_l()
 * @used-by df_bt_entry_is_phtml()
 */
function df_bt_entry_file(array $e):string {return $e['file'];}

/**
 * 2023-07-26
 * @used-by df_caller_m()
 * @used-by df_caller_module()
 */
function df_bt_entry_is_method(array $e):bool {return dfa_has_keys($e, ['class', 'function']);}

/**
 * 2023-07-26
 * @see dfa()
 * @used-by df_caller_module()
 */
function df_bt_entry_is_phtml(array $e):bool {return df_ends_with(df_bt_entry_file($e), '.phtml');}
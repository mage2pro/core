<?php
/**
 * 2023-07-26 "Implement `df_bt_entry_file()`": https://github.com/mage2pro/core/issues/279
 * @see \Df\Qa\Trace\Frame::file()
 * @see \Df\Sentry\Trace::info()
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by df_bt_entry_is_phtml()
 * @used-by df_caller_module()
 * @used-by \Df\Qa\Trace::__construct()
 * @used-by \Df\Sentry\Trace::info()
 */
function df_bt_entry_file(array $e):string {return
	/**
	 * 2023-01-28
	 * 1) The 'file' key can be absent in a stack frame, e.g.:
	 *	{
	 *		"function": "loadClass",
	 *		"class": "Composer\\Autoload\\ClassLoader",
	 *		"type": "->",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 *	{
	 *		"function": "spl_autoload_call",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 * 2) «Argument 1 passed to df_starts_with() must be of the type string, null given,
	 * called in vendor/mage2pro/core/Qa/Trace.php on line 28»: https://github.com/mage2pro/core/issues/186
	 * 3) @see \Df\Qa\Trace\Frame::file()
	 */
	dfa($e, 'file', '')
;}

/**
 * 2023-07-28
 * @see \Df\Qa\Trace\Frame::function_()
 * @used-by df_log_l()
 */
function df_bt_entry_func(array $e):string {return dfa($e, 'function', '');}

/**
 * 2023-07-27 `line` is absent in @see call_user_func() calls.
 * @see \Df\Qa\Trace\Frame::line()
 * @used-by df_bt()
 */
function df_bt_entry_line(array $e):int {return dfa($e, 'line', 0);}

/**
 * 2023-07-26
 * @used-by df_caller_m()
 * @used-by df_caller_module()
 */
function df_bt_entry_is_method(array $e):bool {return dfa_has_keys($e, ['class', 'function']);}

/**
 * 2023-07-26
 * @see \Df\Qa\Trace\Frame::isPHTML()
 * @used-by df_caller_module()
 * @used-by df_log_l()
 */
function df_bt_entry_is_phtml(array $e):bool {return df_ends_with(df_bt_entry_file($e), '.phtml');}
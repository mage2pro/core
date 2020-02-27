<?php
use Exception as E; 

/**
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 */
function df_bt($levelsToSkip = 0) {df_report('bt-{date}-{time}.log', df_bt_s(++$levelsToSkip));}

/**
 * 2019-12-16
 * @used-by df_bt()
 * @used-by df_log_l()
 * @todo It needs to be unified with df_exception_trace().
 * @see df_exception_trace()
 * @param int|E $p
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return string
 */
function df_bt_s($p = 0) {
	/** @var array $bt */
	$bt = $p instanceof E ? $p->getTrace() : array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $p); 
	$r = []; /** @var array $r */
	$traceLength = count($bt); /** @var int $traceLength */
	for ($i = 0; $i < $traceLength; $i++) {
		$s = dfa($bt, $i); /** @var array $s */
		$r[]= [
			'Location' => df_cc(':', df_path_relative(dfa($s, 'file')), dfa($s, 'line'))
			,'Callee' => !$s ? '' : df_cc_method($s)
		];
	}
	return print_r($r, true);
}
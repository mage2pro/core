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
		$cur = dfa($bt, $i); /** @var array $cur */
		$next = dfa($bt, 1 + $i, []); /** @var array(string => string) $nextState */
		$r[]= [
			'Location' => df_cc(':', df_path_relative(dfa($cur, 'file')), dfa($cur, 'line'))
			,'Caller' => !$next ? '' : df_cc_method($next)
			,'Callee' => !$cur ? '' : df_cc_method($cur)
		];
	}
	return print_r($r, true);
}
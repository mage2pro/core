<?php
use Exception as E;
use Df\Qa\Trace;
use Df\Qa\Trace\Formatter;

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
 * @param int|E $p
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return string
 */
function df_bt_s($p = 0) {return Formatter::p(
	new Trace($p instanceof E ? $p->getTrace() : array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $p))
);}
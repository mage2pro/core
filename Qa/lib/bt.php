<?php
use Exception as E;
use Df\Qa\Trace;
use Df\Qa\Trace\Formatter;

/**
 * 2021-10-04
 * @used-by df_bt_has()
 * @used-by df_bt_s()
 * @param E|int|null $p [optional]
 * @return array(array(string => mixed))
 */
function df_bt($p = 0) {return
	$p instanceof E ? $p->getTrace() : array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1 + (int)$p)
;}

/**
 * 2020-05-25
 * @used-by \Df\Framework\Log\Handler\NoSuchEntity::_p()
 * @param string $c
 * @param string|null $m [optional]
 * @param E|null $e [optional]
 * @return bool
 */
function df_bt_has($c, $m = null, E $e = null) {
	list($c, $m) = $m ? [$c, $m] : explode('::', $c);
	return !!df_find(function(array $i) use($c, $m) {return $c === dfa($i, 'class') && $m === dfa($i, 'function');}, df_bt($e));
}

/**
 * 2021-10-04
 * @used-by df_bt_log()
 * @used-by df_bt_s()
 * @param E|int|null|array(array(string => string|int)) $p
 * @return E|int
 */
function df_bt_inc($p) {return is_array($p) || $p instanceof E ? $p : 1 + (int)$p;}

/**
 * @used-by \Df\Core\Exception::__construct()
 * @used-by \Df\Store\Plugin\Model\App\Emulation::beforeStartEnvironmentEmulation()
 * @param int|E $p
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt_log() и т.п.)
 */
function df_bt_log($p = 0) {df_report('bt-{date}-{time}.log', df_bt_s(df_bt_inc($p)));}

/**
 * 2019-12-16
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @used-by \Dfe\Sift\Test\CaseT\API\Account\Decisions::t01()
 * @param int|E|array(array(string => string|int)) $p
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt_log() и т.п.)
 * @return string
 */
function df_bt_s($p = 0) {return Formatter::p(new Trace(df_bt(df_bt_inc($p))));}
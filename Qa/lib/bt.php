<?php

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
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return string
 */
function df_bt_s($levelsToSkip = 0) {
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $levelsToSkip); /** @var array $bt */
	$compactBT = []; /** @var array $compactBT */
	$traceLength = count($bt); /** @var int $traceLength */
	// 2015-07-23
	// 1) Удаляем часть файлового пути до корневой папки Magento.
	// 2) Заменяем разделитель папок на унифицированный.
	$bp = BP . DS; /** @var string $bp */
	$nonStandardDS = DS !== '/'; /** @var bool $nonStandardDS */
	for ($traceIndex = 0; $traceIndex < $traceLength; $traceIndex++) {
		$currentState = dfa($bt, $traceIndex); /** @var array $currentState */
		$nextState = dfa($bt, 1 + $traceIndex, []); /** @var array(string => string) $nextState */
		$file = str_replace($bp, '', dfa($currentState, 'file')); /** @var string $file */
		if ($nonStandardDS) {
			$file = df_path_n($file);
		}
		$compactBT[]= [
			'File' => $file
			,'Line' => dfa($currentState, 'line')
			,'Caller' => !$nextState ? '' : df_cc_method($nextState)
			,'Callee' => !$currentState ? '' : df_cc_method($currentState)
		];
	}
	return print_r($compactBT, true);
}
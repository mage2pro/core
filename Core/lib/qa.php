<?php
/**
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return void
 */
function df_bt($levelsToSkip = 0) {
	/** @var array $bt */
	$bt = array_slice(debug_backtrace(), $levelsToSkip);
	/** @var array $compactBT */
	$compactBT = [];
	/** @var int $traceLength */
	$traceLength = count($bt);
	/**
	 * 2015-07-23
	 * 1) Удаляем часть файлового пути до корневой папки Magento.
	 * 2) Заменяем разделитель папок на унифицированный.
	 */
	/** @var string $bp */
	$bp = BP . DS;
	/** @var bool $nonStandardDS */
	$nonStandardDS = DS !== '/';
	for ($traceIndex = 0; $traceIndex < $traceLength; $traceIndex++) {
		/** @var array $currentState */
		$currentState = df_a($bt, $traceIndex);
		/** @var array(string => string) $nextState */
		$nextState = df_a($bt, 1 + $traceIndex, []);
		/** @var string $file */
		$file = str_replace($bp, '', df_a($currentState, 'file'));
		if ($nonStandardDS) {
			$file = df_path_n($file);
		}
		$compactBT[]= [
			'Файл' => $file
			,'Строка' => df_a($currentState, 'line')
			,'Субъект' =>
				!$nextState
				? ''
				: df_concat_clean('::', df_a($nextState, 'class'), df_a($nextState, 'function'))
			,'Объект' =>
				!$currentState
				? ''
				: df_concat_clean('::', df_a($currentState, 'class'), df_a($currentState, 'function'))
		];
	}
	df_report('bt-{date}-{time}.log', print_r($compactBT, true));
}

/**
 * 2015-04-05
 * @used-by \Df\Core\Exception_InvalidObjectProperty::__construct()
 * @used-by Df_Core_Validator::check()
 * @param mixed $value
 * @param bool $addQuotes [optional]
 * @return string
 */
function df_debug_type($value, $addQuotes = true) {
	/** @var string $result */
	if (is_object($value)) {
		$result = 'объект класса ' . get_class($value);
	}
	else if (is_array($value)) {
		$result = sprintf('массив с %d элементами', count($value));
	}
	else if (is_null($value)) {
		$result = 'NULL';
	}
	else {
		$result = sprintf('%s (%s)', df_string($value), gettype($value));
	}
	return !$addQuotes ? $result : df_quote_russian($result);
}

/**
 * @param string $nameTemplate
 * @param string $message
 * @return void
 */
function df_report($nameTemplate, $message) {
	df_file_put_contents(df_file_name(BP . '/var/log/', $nameTemplate), $message);
}

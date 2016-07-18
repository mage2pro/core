<?php
use Df\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
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
		$currentState = dfa($bt, $traceIndex);
		/** @var array(string => string) $nextState */
		$nextState = dfa($bt, 1 + $traceIndex, []);
		/** @var string $file */
		$file = str_replace($bp, '', dfa($currentState, 'file'));
		if ($nonStandardDS) {
			$file = df_path_n($file);
		}
		$compactBT[]= [
			'Файл' => $file
			,'Строка' => dfa($currentState, 'line')
			,'Субъект' =>
				!$nextState
				? ''
				: df_cc_clean('::', dfa($nextState, 'class'), dfa($nextState, 'function'))
			,'Объект' =>
				!$currentState
				? ''
				: df_cc_clean('::', dfa($currentState, 'class'), dfa($currentState, 'function'))
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
 * 2016-07-18
 * @param E $e
 * @return E
 */
function df_ef(E $e) {
	while ($e->getPrevious()) {
		$e = $e->getPrevious();
	}
	return $e;
}

/**
 * @param E|string $e
 * @return string
 */
function df_ets($e) {
	return is_string($e) ? $e : ($e instanceof DFE ? $e->getMessageRm() : $e->getMessage());
}

/**
 * 2016-03-17
 * @param E $e
 * @return LE
 */
function df_le(E $e) {return $e instanceof LE ? $e : new LE(__(df_ets($e)), $e);}

/**
 * 2016-03-17
 * @param callable $function
 * @return mixed
 * @throws LE
 */
function df_leh($function) {
	/** @var mixed $result */
	try {$result = $function();}
	catch (E $e) {throw df_le($e);}
	return $result;
}

/**
 * @param string $nameTemplate
 * @param string $message
 * @return void
 */
function df_report($nameTemplate, $message) {
	df_file_put_contents(df_file_name(BP . '/var/log/', $nameTemplate), $message);
}

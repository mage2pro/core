<?php
use Df\Core\Exception as DFE;
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\DataObject;
use Magento\Framework\Logger\Monolog;
use Psr\Log\LoggerInterface as ILogger;

/**
 * @param int $levelsToSkip
 * Позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt() и т.п.)
 * @return void
 */
function df_bt($levelsToSkip = 0) {
	/** @var array $bt */
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $levelsToSkip);
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
			'File' => $file
			,'Line' => dfa($currentState, 'line')
			,'Caller' => !$nextState ? '' : df_cc_method($nextState)
			,'Callee' => !$currentState ? '' : df_cc_method($currentState)
		];
	}
	df_report('bt-{date}-{time}.log', print_r($compactBT, true));
}

/**
 * @param DataObject|mixed[]|mixed|E $v
 * @param array(string => mixed) $context [optional]
 * @return void
 */
function df_log($v, array $context = []) {
	if ($v instanceof E) {
		QE::i([QE::P__EXCEPTION => $v, QE::P__SHOW_CODE_CONTEXT => true])->log();
	}
	else {
		$v = df_dump($v);
		/** @var ILogger|Monolog $logger */
		$logger = df_o(ILogger::class);
		$logger->debug($v);
	}
	df_sentry($v, $context);
}

/**
 * @param string $nameTemplate
 * @param string $message
 * @param string $subfolder [optional]
 * @param string $datePartsSeparator [optional]
 * @return void
 */
function df_report($nameTemplate, $message, $subfolder = '', $datePartsSeparator = '-') {
	df_file_put_contents(
		df_file_name(
			df_cc_path(BP . '/var/log', $subfolder)
			,$nameTemplate
			,$datePartsSeparator
		)
		,$message
	);
}
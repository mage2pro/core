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
 */
function df_log($v, array $context = []) {
	df_log_l(null, $v);
	df_sentry(null, $v, $context);
}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Handler::log()
 * @param E $e
 */
function df_log_e($e) {QE::i([QE::P__EXCEPTION => $e, QE::P__SHOW_CODE_CONTEXT => true])->log();}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by dfp_report()
 * @used-by \Df\Payment\W\Action::ignored()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\Dynamics365\API\R::p()
 * @used-by \Dfe\Klarna\Api\Checkout::_html()
 * @param string|object $caller
 * @param string|mixed[]|E $data
 * @param string|null $suffix [optional]
 */
function df_log_l($caller, $data, $suffix = null) {
	if ($data instanceof E) {
		df_log_e($data);
	}
	else {
		/** @var string $method */
		$code = df_package_name_l($caller);
		/** @var string $ext */
		list($ext, $data) = is_string($data) ? ['log', $data] : ['json', df_json_encode_pretty($data)];
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  ".$ext", $data);
	}
}

/**
 * 2017-04-03
 * 2017-04-22
 * С нестроками @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Message::log()
 * @param string $name
 * @param string $message
 */
function df_report($name, $message) {
	df_param_s($message, 1);
	df_file_write(df_file_name(BP . '/var/log', $name), $message);
}
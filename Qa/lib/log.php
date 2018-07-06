<?php
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\DataObject;

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
 * @used-by \Df\Config\Backend::save()
 * @used-by \Df\Config\Backend\Serialized::processA()
 * @used-by \Df\GoogleFont\Fonts\Png::url()
 * @used-by \Df\GoogleFont\Fonts\Sprite::datumPoints()
 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
 * @used-by \Df\OAuth\ReturnT::execute()
 * @used-by \Df\Payment\Method::action()
 * @used-by \Df\Payment\PlaceOrderInternal::message()
 * @used-by \Df\Qa\Message::log()
 * @used-by \Df\Qa\Message\Failure\Error::check()
 * @used-by \Df\Qa\State::__toString()
 * @used-by \Df\Xml\X::addAttributes()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @param DataObject|mixed[]|mixed|E $v
 */
function df_log($v) {
	df_log_l(null, $v);
	df_sentry(null, $v);
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
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
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
		$code = df_package_name_l($caller); /** @var $code $method */
		$data = is_string($data) ? $data : df_json_encode($data);
		$ext = df_starts_with($data, '{') ?  'json' : 'log'; /** @var string $ext */
		df_report(df_ccc('--', "mage2.pro/$code-{date}--{time}", $suffix) .  ".$ext", $data);
	}
}

/**
 * 2017-04-03
 * 2017-04-22
 * С не-строками @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Message::log()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function df_report($f, $m, $append = false) {
	df_param_s($m, 1);
	$f = df_file_ext_def($f, 'log');
	$p = BP . '/var/log'; /** @var string $p */
	df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
}
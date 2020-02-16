<?php
use Df\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\DataObject as _DO;

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
 * @used-by \Dfe\Sift\Observer::f()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null) {df_log_l($m, $v); df_sentry($m, $v);}

/**
 * 2017-01-11
 * @used-by df_error()
 * @used-by df_log_l()
 * @used-by \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()  
 * @param E $e
 * @param string|object|null $m [optional]
 */
function df_log_e($e, $m = null) {QE::i([
	QE::P__EXCEPTION => $e, QE::P__REPORT_NAME_PREFIX => df_report_prefix($m), QE::P__SHOW_CODE_CONTEXT => true
])->log();}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by dfp_report()
 * @used-by \Df\Payment\W\Action::execute()
 * @used-by \Df\Payment\W\Action::ignoredLog()
 * @used-by \Df\Payment\W\Handler::log()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\Klarna\Api\Checkout::_html()
 * @param string|object $m
 * @param string|mixed[]|E $d
 * @param string|bool|null $suffix [optional]
 * @param bool $bt [optional]
 */
function df_log_l($m, $d, $suffix = null, $bt = false) {
	if ($d instanceof E) {
		df_log_e($d, $m);
	}
	else {
		$d = is_string($d) ? $d : df_json_encode($d);
		if (true === $suffix) {
			[$suffix, $bt] = [null, true];
		}
		$ext = !$bt && df_starts_with($d, '{') ? 'json' : 'log'; /** @var string $ext */
		df_report(
			df_ccc('--', 'mage2.pro/' . df_report_prefix($m) . '-{date}--{time}', $suffix) .  ".$ext"
			,df_cc_n($d, !$bt ? null : df_bt_s(1))
		);
	}
}

/**
 * 2017-04-03
 * 2017-04-22
 * С не-строками @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Message::log()
 * @used-by \Inkifi\Mediaclip\H\Logger::l()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function df_report($f, $m, $append = false) {
	if ('' !== $m) {
		df_param_s($m, 1);
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}

/**
 * 2020-01-31
 * @used-by df_log_e()
 * @used-by df_log_l()
 * @param string|object|null $m [optional]
 * @return string|null
 */
function df_report_prefix($m = null) {return !$m ? null : (df_package_name_l($m) ?: df_module_name_lc($m, '-'));}
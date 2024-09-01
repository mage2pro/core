<?php
/**
 * 2017-04-03
 * 2017-04-22
 * С не-строковым значением $m @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @used-by \Df\Core\Text\Regex::throwInternalError()
 * @used-by \Df\Core\Text\Regex::throwNotMatch()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Inkifi\Mediaclip\H\Logger::l()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 */
function df_report(string $f, string $m, bool $append = false):void {
	if (!df_es($m)) {
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}

/**
 * 2020-01-31
 * 2023-07-19
 * «mb_strtolower(): Passing null to parameter #1 ($string) of type string is deprecated
 * in vendor/mage2pro/core/Qa/lib/log.php on line 122»: https://github.com/mage2pro/core/issues/233
 * @used-by df_log_l()
 * @used-by \Df\Framework\Log\Handler\Info::lb()
 * @param string|object|null $m [optional]
 */
function df_report_prefix($m = null, string $pref = ''):string {return df_ccc('--',
	mb_strtolower($pref), !$m ? null : df_module_name_lc($m, '-')
);}
<?php
/**
 * 2017-06-23
 * @used-by df_intl_dic_read()
 * @param string|object $m
 * @param string|null $folder [optional]
 * @param string|null $l [optional]
 * @return string
 */
function df_intl_dic_path($m, $l = null, $folder = null) {return df_cc_path(
	df_module_dir($m), $folder ?: 'i18n', df_locale($l) . '.csv'
);}

/**
 * 2017-06-14 How to parse a CSV file? https://mage2.pro/t/4063  
 * @see df_module_csv2()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string|object $m
 * @param string|null $folder [optional]
 * @param string|null $locale [optional]
 * @return array(string => string)|mixed
 */
function df_intl_dic_read($m, $locale = null, $folder = null) {
	$p = df_intl_dic_path($m, $locale, $folder); /** @var string $p */
	return df_try(function() use($p) {return df_csv_o()->getDataPairs($p);}, [])
;}

/**
 * 2017-06-23
 * It works similar to:
 * @see \Magento\Framework\File\Csv::saveData()
 * @see \Magento\Framework\Filesystem\Driver\File::filePutCsv()
 * But it also makes the single-word dictionary entries quoted:
 * https://stackoverflow.com/questions/2489553
 * https://gist.github.com/anonymous/223ea7353626bc6a6a9e#file-csvenclosed-php
 * @param string|object $m
 * @param array(string => string) $data
 * @param string|null $folder [optional]
 * @param string|null $locale [optional]
 */
function df_intl_dic_write($m, array $data, $locale = null, $folder = null) {
	/** @var string $path */
	$path = df_intl_dic_path($m, $locale, $folder);
	/** @var resource $h */
	$h = fopen($path, 'w');
	df_map_k(function($k, $v) use($h) {/** @var string $k *//** @var string $v */
		fputcsv($h, array_map(function($s) {$d = '"'; return
			$d . str_replace($d, "$d$d", $s) . $d
		;}, [$k, $v]), ',', chr(0));
	}, $data);
	fclose($h);
	file_put_contents($path, str_replace(chr(0), '', file_get_contents($path)));
;}
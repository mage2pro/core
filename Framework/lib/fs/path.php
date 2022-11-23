<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;

/**
 * 2016-12-23
 * Удаляет из сообщений типа
 * «Warning: Division by zero in C:\work\mage2.pro\store\vendor\mage2pro\stripe\Method.php on line 207»
 * файловый путь до папки Magento.
 * @used-by df_xts()
 * @used-by df_xtsd()
 */
function df_adjust_paths_in_message(string $m):string {
	$bpLen = mb_strlen(BP); /** @var int $bpLen */
	do {
		$begin = mb_strpos($m, BP); /** @var int|false $begin */
		if (false === $begin) {
			break;
		}
		$end = mb_strpos($m, '.php', $begin + $bpLen); /** @var int|false $end */
		if (false === $end) {
			break;
		}
		$end += 4; # 2016-12-23 It is the length of the «.php» suffix.
		$m =
			mb_substr($m, 0, $begin)
			# 2016-12-23 I use `+ 1` to cut off a slash («/» or «\») after BP.
			. df_path_n(mb_substr($m, $begin + $bpLen + 1, $end - $begin - $bpLen - 1))
			. mb_substr($m, $end)
		;
	} while(true);
	return $m;
}

/**
 * 2015-12-06 A @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath() result ends with «/».
 * @used-by df_media_path_absolute()
 * @used-by df_product_images_path()
 * @used-by df_sync()
 */
function df_path_absolute(string $p, string $suffix = ''):string {return df_prepend(df_trim_ds_left($suffix), df_fs_r($p)->getAbsolutePath());}

/**
 * 2017-05-08
 * @used-by \Df\Framework\Plugin\Session\SessionManager::beforeStart()
 * @used-by \Df\Sentry\Trace::info()
 * @param string $p
 */
function df_path_is_internal($p):bool {return '' === $p || df_starts_with(df_path_n($p), df_path_n(BP));}

/**
 * Заменяет все сиволы пути на /
 * 2021-12-17 https://3v4l.org/8iP17
 * @see df_path_n_real()
 * @used-by df_adjust_paths_in_message()
 * @used-by df_bt_s()
 * @used-by df_class_file()
 * @used-by df_explode_path()
 * @used-by df_file_name()
 * @used-by df_path_is_internal()
 * @used-by df_path_relative()
 * @used-by df_product_image_url()
 * @used-by \Df\SampleData\Model\Dependency::getModuleComposerPackageMy()
 * @used-by \Df\Sentry\Client::needSkipFrame()
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @param string $p
 */
function df_path_n($p):string {return str_replace(['\/', '\\'], '/', $p);}

/**
 * 2016-12-30 It replaces all path delimiters with @uses DS
 * 2021-12-17 https://3v4l.org/OGUh6
 * @see df_path_n()
 * @param string $p
 */
function df_path_n_real($p):string {return str_replace(['\/', '\\', '/'], DS, $p);}

/**
 * 2015-12-06 It trims the ending «/».
 * @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath() produces a result with a trailing «/».
 * @used-by df_file_write()
 * @used-by df_media_path_relative
 * @used-by df_product_images_path_rel()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by \Df\Sentry\Trace::info()
 * @param string $p
 * @param string $b [optional]
 */
function df_path_relative($p, $b = DL::ROOT):string {return df_trim_text_left(
	df_trim_ds_left(df_path_n($p)), df_trim_ds_left(df_fs_r($b)->getAbsolutePath())
);}
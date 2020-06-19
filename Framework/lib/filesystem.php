<?php
use Magento\Framework\App\Filesystem\DirectoryList as DL;
use Magento\Framework\Filesystem\Directory\Read as DirectoryRead;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReadFactory;
use Magento\Framework\Filesystem\Directory\ReadInterface as IDirectoryRead;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\Filesystem\Directory\WriteInterface as IDirectoryWrite;
use Magento\Framework\Filesystem\File\Read as FileRead;
use Magento\Framework\Filesystem\File\ReadInterface as IFileRead;
use Magento\Framework\Filesystem\File\Write as FileWrite;
use Magento\Framework\Filesystem\File\WriteInterface as IFileWrite;
use Magento\Framework\Filesystem\Io\File as File;
use Magento\Framework\Filesystem\Io\Sftp;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * 2017-12-13
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @param string $p
 * @return string
 */
function df_add_ds_right($p) {return df_trim_ds_right($p) . '/';}

/**
 * 2016-12-23
 * Удаляет из сообщений типа
 * «Warning: Division by zero in C:\work\mage2.pro\store\vendor\mage2pro\stripe\Method.php on line 207»
 * файловый путь до папки Magento.
 * @param string $m
 * @return string
 */
function df_adjust_paths_in_message($m) {
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
		$end += 4; // 2016-12-23 It is the length of the «.php» suffix.
		$m =
			mb_substr($m, 0, $begin)
			// 2016-12-23 I use `+ 1` to cut off a slash («/» or «\») after BP.
			. df_path_n(mb_substr($m, $begin + $bpLen + 1, $end - $begin - $bpLen - 1))
			. mb_substr($m, $end)
		;
	} while(true);
	return $m;
}

/**
 * 2019-02-24
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @return File
 */
function df_file() {return df_o(File::class);}

/**
 * 2015-11-28 http://stackoverflow.com/a/10368236
 * @used-by df_asset_create()  
 * @used-by df_file_ext_def()
 * @param string $f
 * @return string
 */
function df_file_ext($f) {return pathinfo($f, PATHINFO_EXTENSION);}

/**
 * 2018-07-06       
 * @used-by df_report()
 * @param string $f
 * @param string $ext
 * @return string
 */
function df_file_ext_def($f, $ext) {return ($e = df_file_ext($f)) ? $f : df_trim_right($f, '.') . ".$ext";}

/**
 * Возвращает неиспользуемое имя файла в заданной папке $directory по заданному шаблону $template.
 * Результатом всегда является непустая строка.
 * @used-by df_report()
 * @param string $directory
 * @param string $template
 * @param string $ds [optional]
 * @return string
 */
function df_file_name($directory, $template, $ds = '-') {
	// 2016-11-09
	// Отныне $template может содержать файловый путь:
	// в этом случае этот файловый путь убираем из $template и добавляем к $directory.
	$directory = df_path_n($directory);
	$template = df_path_n($template);
	if (df_contains($template, '/')) {
		$templateA = explode('/', $template); /** @var string[] $templateA */
		$template = array_pop($templateA);
		$directory = df_cc_path($directory, $templateA);
	}
	/** @var string $result */
	$counter = 1; /** @var int $counter */
	$hasOrderingPosition = df_contains($template, '{ordering}');/** @var bool $hasOrderingPosition */
	$now = \Zend_Date::now()->setTimezone('Europe/Moscow'); /** @var \Zend_Date $now */
	/** @var array(string => string) $vars */
	$vars = df_map_k(function($k, $v) use($ds, $now) {return
		df_dts($now, implode($ds, $v))
	;}, ['date' => ['y', 'MM', 'dd'], 'time' => ['HH', 'mm'], 'time-full' => ['HH', 'mm', 'ss']]);
	/**
	 * 2016-11-09
	 * @see \Zend_Date неправильно работает с миллисекундами:
	 * всегда возвращает 0 вместо реального количества миллисекунд.
	 * Так происходит из-за дефекта в методах
	 * @see \Zend_Date::addMilliSecond()
	 * @see \Zend_Date::setMilliSecond()
	 * Там такой код:
	 *		list($milli, $time) = explode(" ", microtime());
	 *		$milli = intval($milli);
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/lib/Zend/Date.php#L4490-L4491
	 * Этот код ошибочен, потому что после первой операции
	 * $milli содержит дробное значение меньше 1, например: 0.653...
	 * А вторая операция тупо делает из этого значения 0.
	 */
	$vars['time-full-ms'] = implode($ds, [$vars['time-full'],
		sprintf('%02d', round(100 * df_first(explode(' ', microtime()))))
	]);
	while (true) {
		/** @var string $fileName */
		$fileName = df_var($template, ['ordering' => sprintf('%03d', $counter)] + $vars);
		$fileFullPath = $directory . DS . $fileName; /** @var string $fileFullPath */
		if (!file_exists($fileFullPath)) {
			/**
			 * Раньше здесь стояло file_put_contents,
			 * и иногда почему-то возникал сбой:
			 * failed to open stream: No such file or directory.
			 * Может быть, такой сбой возникает, если папка не существует?
			 */
			$result = $fileFullPath;
			break;
		}
		else {
			if ($counter > 999) {
				df_error("Счётчик достиг предела ({$counter}).");
			}
			else {
				$counter++;
				/**
				 * Если в шаблоне имени файла
				 * нет переменной «{ordering}» — значит, надо добавить её,
				 * чтобы в следующей интерации имя файла стало уникальным.
				 * Вставляем «{ordering}» непосредственно перед расширением файла.
				 * Например, rm.shipping.log преобразуем в rm.shipping-{ordering}.log
				 */
				if (!$hasOrderingPosition && (2 === $counter)) {
					/** @var string[] $fileNameTemplateExploded */
					$fileNameTemplateExploded = explode('.', $template);
					/** @var int $secondFromLastPartIndex*/
					$secondFromLastPartIndex =  max(0, count($fileNameTemplateExploded) - 2);
					/** @var string $secondFromLastPart */
					$secondFromLastPart = dfa($fileNameTemplateExploded, $secondFromLastPartIndex);
					df_assert_sne($secondFromLastPart);
					$fileNameTemplateExploded[$secondFromLastPartIndex] =
						implode('--', [$secondFromLastPart, '{ordering}'])
					;
					$template = df_assert_ne($template, implode('.', $fileNameTemplateExploded));
				}
			}
		}
	}
	return df_path_n($result);
}

/**
 * 2015-12-08
 * @param string $p
 * @param string $relativeFileName
 * @return string
 */
function df_file_read($p, $relativeFileName) {
	$reader = df_fs_r($p); /** @var DirectoryRead|IDirectoryRead $reader */
	$file = $reader->openFile($relativeFileName, 'r'); /** @var IFileRead|FileRead $file */
	try {$r = $file->readAll();} /** @var string $r */
	finally {$file->close();}
	return $r;
}

/**
 * 2015-11-29
 * 2015-11-30
 * Иерархия папок создаётся автоматически:
 * @see \Magento\Framework\Filesystem\Directory\Write::openFile()
 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Filesystem/Directory/Write.php#L247
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * 2018-07-06 The `$append` parameter has been added. 
 * 2020-02-14 If $append is `true`, then $contents will be written on a new line. 
 * @used-by df_report()
 * @used-by df_sync()
 * @used-by \Df\GoogleFont\Font\Variant::ttfPath()
 * @used-by \Df\GoogleFont\Fonts\Png::create()
 * @used-by \Df\GoogleFont\Fonts\Sprite::draw()
 * @param string|string[] $p
 * @param string $contents
 * @param bool $append [optional]
 */
function df_file_write($p, $contents, $append = false) {
	/**
	 * 2017-04-22
	 * С не-строками @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
	 * потому что там стоит код: $lenData = strlen($data);
	 */
	df_param_s($contents, 1);
	/** @var string $type */ /** @var string $relative */
	// 2020-03-02
	// The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	// https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	// We should support PHP 7.0.
	list($type, $relative) = is_array($p) ? $p : [DL::ROOT, df_path_relative($p)];
	$writer = df_fs_w($type); /** @var DirectoryWrite|IDirectoryWrite $writer */
	/**
	 * 2018-07-06
	 * Note 1.
	 * https://php.net/manual/function.fopen.php#refsect1-function.fopen-parameters
	 * 'w':	Open for writing only;
	 * 		place the file pointer at the beginning of the file and truncate the file to zero length.
	 * 		If the file does not exist, attempt to create it.
	 * 'a'	Open for writing only; place the file pointer at the end of the file.
	 * 		If the file does not exist, attempt to create it.
	 * 		In this mode, fseek() has no effect, writes are always appended.
	 */
	$file = $writer->openFile($relative, $append ? 'a' : 'w'); /** @var IFileWrite|FileWrite $file */
	/**
	 * 2015-11-29
	 * By analogy with @see \Magento\MediaStorage\Model\File\Storage\Synchronization::synchronize()
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/MediaStorage/Model/File/Storage/Synchronization.php#L61-L68
	 * Обратите внимание, что к реализации этого метода у меня аж 4 замечания:
	 *
	 * 1) https://mage2.pro/t/274
	 * «\Magento\MediaStorage\Model\File\Storage\Synchronization::synchronize() wrongly leaves a file in the locked state in case of an exception»
	 *
	 * 2) https://mage2.pro/t/271
	 * «\Magento\MediaStorage\Model\File\Storage\Synchronization::synchronize() suppresses its exceptions for a questionably reason»
	 *
	 * 3) https://mage2.pro/t/272
	 * «\Magento\MediaStorage\Model\File\Storage\Synchronization::synchronize() duplicates the code in the try and catch blocks, propose to use a «finally» block»
	 *
	 * 4) https://mage2.pro/t/273
	 * «\Magento\MediaStorage\Model\File\Storage\Synchronization::synchronize() contains a wrong PHPDoc comment for the $file variable»
	 */
	try {
		$file->lock();
		try {
			/**
			 * 2018-07-06
			 * Note 1. https://stackoverflow.com/a/4857194
			 * Note 2.
			 * @see ftell() and @see \Magento\Framework\Filesystem\File\Read::tell() do not work here
			 * even if the file is opened in the `a+` mode:
			 * https://php.net/manual/function.ftell.php#116885
			 * «When opening a file for reading and writing via fopen('file','a+')
			 * the file pointer should be at the end of the file.
			 * However ftell() returns int(0) even if the file is not empty.»
			 */
			if ($append && 0 !== filesize(BP . "/$relative")) {
				// 2018-07-06 «PHP fwrite new line» https://stackoverflow.com/a/15130410
				$contents = PHP_EOL . $contents;
			}
			$file->write($contents);
		}
		finally {$file->unlock();}
	}
	finally {$file->close();}
}

/**
 * 2015-11-29
 * @return \Magento\Framework\Filesystem
 */
function df_fs() {return df_o(\Magento\Framework\Filesystem::class);}

/**
 * 2017-04-03
 * Портировал из РСМ. Никем не используется.
 * @param string $p
 */
function df_fs_delete($p) {File::rmdirRecursive(df_param_sne($p, 0));}

/**
 * 2019-08-23
 * @used-by df_fs_etc()
 * @return DL
 */
function df_fs_dl() {return df_o(DL::class);}

/**
 * 2019-08-23
 * @used-by \Dfe\Color\Image::__construct()
 * @param string $p [optional]
 * @return DL
 */
function df_fs_etc($p = '') {return df_cc_path(df_fs_dl()->getPath(DL::CONFIG), df_trim_ds_left($p));}

/**
 * 2015-11-29
 * Преобразует строку таким образом,
 * чтобы её было безопасно и удобно использовать в качестве имени файла или папки.
 * http://stackoverflow.com/a/2021729
 * 2017-02-09
 * Сегодня заметил, что эта функция удаляет пробелы, но сохраняет символы Unicode:
 * '歐付寶 all/Pay' => '歐付寶-allPay'

 * Пример №1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  all/Pay
 * @see df_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Пример №2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  allPay
 * @see df_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * @param string $n
 * @param string $spaceSubstitute [optional]
 * @return string
 */
function df_fs_name($n, $spaceSubstitute = '-') {
	$n = str_replace(' ', $spaceSubstitute, $n);
	// Remove anything which isn't a word, whitespace, number
	// or any of the following caracters -_~,;:[]().
	// If you don't need to handle multi-byte characters
	// you can use preg_replace rather than mb_ereg_replace
	// Thanks @Łukasz Rysiak!
	$n = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $n);
	// Remove any runs of periods (thanks falstro!)
	return mb_ereg_replace("([\.]{2,})", '', $n);
}

/**
 * 2015-11-30
 * @used-by df_media_reader()
 * @param string $p
 * @return DirectoryRead|IDirectoryRead
 */
function df_fs_r($p) {return df_fs()->getDirectoryRead($p);}

/**
 * 2020-06-16
 * @used-by \Df\SampleData\Model\Dependency::getModuleComposerPackageParent()
 * @return DirectoryReadFactory
 */
function df_fs_rf() {return df_o(DirectoryReadFactory::class);}

/**
 * 2015-11-29
 * 2017-04-03 The possible directory types for filesystem operations: https://mage2.pro/t/3591
 * @used-by df_file_write()
 * @used-by df_media_writer()
 * @used-by df_sync()
 * @param string $type
 * @return DirectoryWrite|IDirectoryWrite
 */
function df_fs_w($type) {return df_fs()->getDirectoryWrite($type);}

/**
 * 2015-12-06
 * @used-by df_media_path_absolute()
 * @used-by df_product_image_path2abs()
 * @used-by df_sync()
 * @param string $p
 * @param string $suffix [optional]
 * @return string
 * Результат вызова @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath()
 * завершается на «/»
 */
function df_path_absolute($p, $suffix = '') {return df_prepend(df_trim_ds_left($suffix), df_fs_r($p)->getAbsolutePath());}

/**
 * 2017-05-08
 * @used-by \Df\Framework\Plugin\Session\SessionManager::beforeStart()
 * @param string $p
 * @return bool
 */
function df_path_is_internal($p) {return '' === $p || df_starts_with(df_path_n($p), df_path_n(BP));}

/**
 * Заменяет все сиволы пути на /
 * @used-by df_bt_s()
 * @used-by df_class_file()
 * @used-by df_explode_path()
 * @used-by df_adjust_paths_in_message()
 * @used-by df_file_name()
 * @used-by df_path_is_internal()
 * @used-by df_path_relative()
 * @used-by \Df\SampleData\Model\Dependency::getModuleComposerPackageMy()
 * @used-by \Df\Sentry\Client::needSkipFrame()
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @used-by \Dfr\Core\Realtime\Dictionary\ModulePart\Block::matchTemplate()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @param string $p
 * @return string
 */
function df_path_n($p) {return str_replace('//', '/', str_replace('\\', '/', $p));}

/**
 * 2016-12-30
 * Заменяет все сиволы пути на BP
 * @param string $p
 * @return string
 */
function df_path_n_real($p) {return strtr($p, ['\\' => DS, '/' => DS]);}

/**
 * 2015-12-06
 * Левый «/» мы убираем.
 * Результат вызова @uses \Magento\Framework\Filesystem\Directory\Read::getAbsolutePath() завершается на «/».
 * @used-by df_file_write()
 * @used-by df_media_path_relative
 * @used-by df_xml_load_file()  
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @param string $p
 * @param string $b [optional]
 * @return string
 */
function df_path_relative($p, $b = DL::ROOT) {return df_trim_text_left(df_trim_ds_left(
	df_path_n($p)), df_trim_ds_left(df_fs_r($b)->getAbsolutePath()
));}

/**
 * 2019-02-24
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
 * @return Sftp
 */
function df_sftp() {return df_o(Sftp::class);}

/**
 * 2015-04-01
 * Раньше алгоритм был таким: return preg_replace('#\.[^.]*$#', '', $file)
 * Новый вроде должен работать быстрее?
 * http://stackoverflow.com/a/22537165
 * 2019-08-09
 * 1) preg_replace('#\.[^.]*$#', '', $file) preserves the full path.
 * 2) pathinfo($file, PATHINFO_FILENAME) strips the full path and returns the base name only.
 * @used-by wolf_u2n()
 * @used-by \Justuno\M2\Controller\Js::execute()
 * @used-by \Wolf\Filter\Block\Navigation::getConfigJson()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string $s
 * @return mixed
 */
function df_strip_ext($s) {return preg_replace('#\.[^.]*$#', '', $s);}

/**
 * 2016-10-14
 * @used-by df_url_bp()
 * @param string $p
 * @return string
 */
function df_trim_ds($p) {return df_trim($p, '/\\');}

/**
 * 2015-11-30
 * @used-by df_fs_etc()
 * @used-by df_path_absolute()
 * @used-by df_path_relative()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @used-by \Dfe\Salesforce\Test\Basic::url()
 * @param string $p
 * @return string
 */
function df_trim_ds_left($p) {return df_trim_left($p, '/\\');}

/**
 * 2016-10-14
 * @used-by df_add_ds_right()
 * @used-by df_magento_version_remote()
 * @used-by \Df\Payment\Method::canUseForCountryP()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::build()
 * @used-by \Dfe\BlackbaudNetCommunity\Url::check()
 * @param string $p
 * @return string
 */
function df_trim_ds_right($p) {return df_trim_right($p, '/\\');}
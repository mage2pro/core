<?php
use Closure as F;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

/**
 * 2015-08-14
 * https://mage2.pro/t/57
 * https://mage2.ru/t/92
 * 2015-09-02
 * @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * uses `/` insteads @see DIRECTORY_SEPARATOR as the path separator, so I use `/` too.
 * 2016-11-17
 * 1) $m could be:
 * 		1.1) a module name: «A_B»
 * 		1.2) a class name: «A\B\C».
 * 		1.3) an object: it comes down to the case 2 via @see get_class()
 * 		1.4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * 2) The function does not cache its result because is is already cached by
 * @uses \Magento\Framework\Module\Dir\Reader::getModuleDir().
 * 2019-12-31
 * 1) The result is the full filesystem path of the module, e.g.
 * «C:/work/clients/royalwholesalecandy.com-2019-12-08/code/vendor/mage2pro/core/Intl».
 * 2) The allowed $type argument values are:
 * @see \Magento\Framework\Module\Dir::MODULE_ETC_DIR
 * @see \Magento\Framework\Module\Dir::MODULE_I18N_DIR
 * @see \Magento\Framework\Module\Dir::MODULE_VIEW_DIR
 * @see \Magento\Framework\Module\Dir::MODULE_CONTROLLER_DIR
 * @see \Magento\Framework\Module\Dir::getDir():
 *	if ($type) {
 *		if (!in_array($type, [
 *			self::MODULE_ETC_DIR,
 *			self::MODULE_I18N_DIR,
 *			self::MODULE_VIEW_DIR,
 *			self::MODULE_CONTROLLER_DIR
 *		])) {
 *		throw new \InvalidArgumentException("Directory type '{$type}' is not recognized.");
 *	}
 *		$path .= '/' . $type;
 *	}
 * https://github.com/magento/magento2/blob/2.3.3/lib/internal/Magento/Framework/Module/Dir.php#L54-L65
 * @used-by df_intl_dic_path()
 * @used-by df_module_path()
 * @used-by df_module_path_etc()
 * @used-by df_test_file()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::req()
 * @param string|object|null $m
 * @throws InvalidArgumentException
 */
function df_module_dir($m, string $type = ''):string {
	if ('Magento_Framework' !== ($m = df_module_name($m))) {
		$r = df_module_dir_reader()->getModuleDir($type, $m);
	}
	else {
		$r = df_framework_path();
		# 2019-12-31 'Magento_Framework' is not a module, so it does not have subpaths specific for modules.
		df_assert(!$type);
	}
	return $r;
}

/**
 * 2023-09-28
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_module_file_read()
 * @used-by \Df\Sentry\Client::send_http()
 * @param string|object|null $m
 * @param F|bool|mixed $onE [optional]
 */
function df_module_file_name($m, string $name, string $ext = '', $onE = true):string {
	$r = df_module_path_etc($m, df_file_ext_add($name, $ext));
	return df_fts(df_try(
		function() use($r):string {df_assert(file_exists($r), "The required file «{$r}» is absent."); return $r;}, $onE
	));
}

/**
 * 2015-11-15
 * 2015-09-02
 * @uses df_module_dir() and indirectly called @see \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * use `/` insteads @see DIRECTORY_SEPARATOR as the path separator, so I use `/` too.
 * 2016-11-17
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @param string|object|null $m
 * @throws InvalidArgumentException
 */
function df_module_path($m, string $localPath = ''):string {return df_cc_path(df_module_dir($m), $localPath);}

/**
 * 2016-07-19
 * 2015-09-02
 * @uses df_module_dir() and indirectly called @see \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * use `/` insteads @see DIRECTORY_SEPARATOR as the path separator, so I use `/` too.
 * 2016-11-17
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_module_file_name()
 * @param string|object|null $m
 * @throws InvalidArgumentException
 */
function df_module_path_etc($m, string $localPath = ''):string {return df_cc_path(
	df_module_dir($m, Dir::MODULE_ETC_DIR), $localPath
);}
<?php
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

/**
 * 2017-09-01
 * @see df_intl_dic_read()
 * @see df_module_enum()
 * @see df_module_json()
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by \Dfe\PostFinance\W\Event::optionTitle()
 * @used-by \Dfe\Qiwi\API\Validator::codes()
 * @param string|object|null $m
 * @return array(string => mixed)
 */
function df_module_csv2($m, string $name, bool $req = true):array {return df_module_file($m, $name, 'csv', $req,
	function(string $f):array {return df_csv_o()->getDataPairs($f);}
);}

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
		df_assert(!$type); # 2019-12-31 'Magento_Framework' is not a module, so it does not have subpaths specific for modules.
	}
	return $r;
}

/**
 * 2019-12-31
 * @used-by df_module_dir()
 */
function df_module_dir_reader():Reader {return df_o(Reader::class);}

/**
 * 2020-02-02
 * @see df_module_csv2()
 * @see df_module_json()
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by \Dfe\Sift\PM\FE::onFormInitialized()
 * @param string|object|null $m
 * @return array(string => mixed)
 */
function df_module_enum($m, string $name, bool $req = true):array {return df_module_file($m, $name, 'txt', $req,
	function(string $f):array {return df_explode_n(df_contents($f));}
);}

/**
 * 2017-09-01
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_module_csv2()
 * @used-by df_module_json()  
 * @used-by \Df\Sentry\Client::send_http()
 * @param string|object|null $m
 * @return array(string => mixed)
 */
function df_module_file($m, string $name, string $ext = '', bool $req = true, Closure $parser = null):array {return dfcf(
	function($m, string $name, string $ext = '', $req = true, Closure $parser = null):array {return
		file_exists($f = df_module_path_etc($m, df_file_ext_add($name, $ext)))
			? (!$parser ? $f : $parser($f))
			: (!$req ? [] : df_error('The required file «%1» is absent.', $f))
	;}, func_get_args()
);}

/**
 * 2017-01-27
 * $m could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @see df_module_csv2()
 * @see df_module_enum()
 * @used-by df_currency_nums()
 * @used-by df_sentry_m()
 * @used-by vendor/mage2pro/portal-stripe/view/frontend/templates/page/settings.phtml
 * @used-by \Df\PaypalClone\W\Event::statusT()
 * @used-by \Dfe\AllPay\W\Event::tlByCode()
 * @used-by \Dfe\CheckoutCom\Source\Prefill::config()
 * @used-by \Dfe\IPay88\Source\Option::all()
 * @used-by \Dfe\IPay88\Source\Option::map()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @param string|object|null $m
 * @return array(string => mixed)
 */
function df_module_json($m, string $name, bool $req = true):array {return df_module_file($m, $name, 'json', $req,
	function(string $f):array {return df_json_file_read($f);}
);}

/**
 * 2015-11-15
 * 2015-09-02
 * @uses df_module_dir() and indirectly called @see \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * use `/` insteads @see DIRECTORY_SEPARATOR as the path separator, so I use `/` too.
 * 2016-11-17
 * $m could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
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
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_module_file()
 * @param string|object|null $m
 * @throws InvalidArgumentException
 */
function df_module_path_etc($m, string $localPath = ''):string {return df_cc_path(
	df_module_dir($m, Dir::MODULE_ETC_DIR), $localPath
);}
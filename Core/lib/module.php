<?php
use Magento\Framework\Module\Dir as ModuleDir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;
/**
 * 2017-09-01
 * @see df_intl_dic_read()
 * @see df_module_json()
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @used-by \Dfe\PostFinance\W\Event::optionTitle()
 * @used-by \Dfe\Qiwi\API\Validator::codes()
 * @param string|object $m
 * @param string $name
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function df_module_csv2($m, $name, $req = true) {return df_module_file($m, $name, 'csv', $req,
	function($f) {return df_csv_o()->getDataPairs($f);}
);}

/**
 * 2015-08-14
 * https://mage2.pro/t/57
 * https://mage2.ru/t/92
 *
 * 2015-09-02
 * Метод @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * в качестве разделителя путей использует не DIRECTORY_SEPARATOR, а /
 *
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * Результат намеренно не кэшируем,
 * потому что @uses \Magento\Framework\Module\Dir\Reader::getModuleDir() его отлично сам кэширует.
 *
 * @used-by df_test_file()
 * @used-by \Df\Core\O::modulePath()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string|object $m
 * @param string $type [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_dir($m, $type = '') {
	$reader = df_o(ModuleDirReader::class); /** @var ModuleDirReader $reader */
	return $reader->getModuleDir($type, df_module_name($m));
}

/**
 * 2017-06-21
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string $m
 * @return bool
 */
function df_module_exists($m) {return !!df_modules_o()->getOne($m);}

/**
 * 2017-09-01
 * @used-by df_module_csv2()
 * @used-by df_module_json()
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @param string|object $m
 * @param string $name
 * @param string $ext
 * @param bool $req
 * @param \Closure $parser
 * @return array(string => mixed)
 */
function df_module_file($m, $name, $ext, $req, \Closure $parser) {return dfcf(
	function($m, $name, $ext, $req, $parser) {return
		file_exists($f = df_module_path_etc($m, "$name.$ext")) ? $parser($f) :
			(!$req ? [] : df_error('The required file «%1» is absent.', $f))
	;}, func_get_args()
);}

/**
 * 2017-01-27
 * @see df_module_csv2()
 * @used-by \Df\PaypalClone\W\Event::statusT()
 * @used-by \Dfe\CheckoutCom\Source\Prefill::_config()
 * @used-by \Dfe\IPay88\Source\Option::all()
 * @used-by \Dfe\IPay88\Source\Option::map()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 * @param string|object $m
 * @param string $name
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function df_module_json($m, $name, $req = true) {return df_module_file($m, $name, 'json', $req,
	function($f) {return df_json_decode(file_get_contents($f));}
);}

/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 *
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * 2016-10-26
 * Функция успешно работает с короткими именами классов:
 * «A\B\C» => «A_B»
 * «A_B» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 *
 * 2017-01-27
 * Так как «A_B» => «A_B», то функция успешно работает с именем модуля:
 * она просто возвращает его без изменений.
 * Таким образом, функция допускает на входе:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 *
 * @used-by df_asset_name()
 * @used-by df_block_output()
 * @used-by df_con()
 * @used-by df_fe_init()
 * @used-by df_js()
 * @used-by df_module_dir()
 * @used-by df_module_name_c()
 * @used-by df_package()
 * @used-by df_route()
 * @used-by df_sentry_module()
 * @used-by df_widget()
 * @used-by \Df\Framework\Plugin\View\Element\AbstractBlock::afterGetModuleName()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Shipping\Method::s()
 * @used-by \Df\Sso\CustomerReturn::execute()
 * @param string|object|null $c [optional]
 * @param string $del [optional]
 * @return string
 */
function df_module_name($c = null, $del = '_') {return dfcf(function($c, $del) {return
	implode($del, array_slice(df_explode_class($c), 0, 2))
;}, [$c ? df_cts($c) : 'Df\Core', $del]);}

/**
 * 2017-01-04
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class()
 * @used-by dfs()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Action::module()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @param string|object|null $c [optional]
 * @return string
 */
function df_module_name_c($c = null) {return df_module_name($c, '\\');}

/**
 * 2016-08-28 «Dfe\AllPay\W\Handler» => «AllPay»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_module_name_short($c) {return dfcf(function($c) {return df_explode_class($c)[1];}, [df_cts($c)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 1) A module name. E.g.: «A_B».
 * 2) A class name. E.g.: «A\B\C».
 * 3) An object. It will be treated as case 2 after @see get_class() 
 * @used-by \Df\Core\Exception::reportNamePrefix()
 * @used-by \Df\Payment\Method::codeS()
 * @used-by \Df\Shipping\Method::getCarrierCode()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_module_name_lc($c, $del = '_') {return implode(
	$del, df_explode_class_lc_camel(df_module_name_c($c))
);}

/**
 * 2015-11-15
 * 2015-09-02
 * Метод @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * и, соответственно, @uses df_module_dir()
 * в качестве разделителя путей использует не DIRECTORY_SEPARATOR, а /,
 * поэтому и мы поступаем так же.
 *
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * @param string|object $m
 * @param string $localPath [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_path($m, $localPath = '') {return df_cc_path(df_module_dir($m), $localPath);}

/**
 * 2016-07-19
 * 2015-09-02
 * Метод @uses \Magento\Framework\Module\Dir\Reader::getModuleDir()
 * и, соответственно, @uses df_module_dir()
 * в качестве разделителя путей использует не DIRECTORY_SEPARATOR, а /,
 * поэтому и мы поступаем так же.
 *
 * 2016-11-17
 * В качестве $m можно передавать:
 * 1) Имя модуля. «A_B»
 * 2) Имя класса. «A\B\C»
 * 3) Объект класса.
 *
 * @used-by df_module_file()

 * @param string|object $m
 * @param string $localPath [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_path_etc($m, $localPath = '') {return df_cc_path(
	df_module_dir($m, ModuleDir::MODULE_ETC_DIR), $localPath
);}

/**
 * 2017-04-01
 * @used-by df_module_exists()
 * @used-by dfe_modules()
 * @return IML|ML
 */
function df_modules_o() {return df_o(IML::class);}

/**
 * 2017-06-21
 * @used-by dfe_modules()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string $p
 * @return string[]
 */
function df_modules_p($p) {return dfcf(function($p) {return df_sort_names(array_filter(
	df_modules_o()->getNames(), function($m) use($p) {return df_starts_with($m, $p);}
));}, [$p]);}

/**
 * 2017-05-05 It returns an array like [«Dfe_PortalStripe»]]].
 * 2017-06-19 I intentionally do not return the «Dfr_*» modules, because they are not extensions
 * (they are used for language translation).
 * @used-by dfe_packages()
 * @return string[]
 */
function dfe_modules() {return df_modules_p('Dfe_');}

/**
 * 2017-04-01
 * Возвращает массив вида ['AllPay 1.5.3' => [информация из локального composer.json]].
 * Ключи массива не содержат приставку «Dfe_».
 * @used-by dfe_modules_log()
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @return array(string => array)
 */
function dfe_modules_info() {return dfcf(function() {return df_map_kr(dfe_packages(), function($m, $p) {return [
	df_cc_s(substr($m, 4), $p['version']), $p
];});});};

/**
 * 2017-04-01
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 */
function dfe_modules_log() {df_sentry(null
	,sprintf('%s: %s', df_domain_current(), df_csv_pretty(array_keys(dfe_modules_info())))
	,['extra' => ['Backend URL' => df_url_backend_ns()]]
);}
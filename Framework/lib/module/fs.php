<?php
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;

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
 * @used-by \Dfe\Vantiv\T\CaseT\Charge::req()
 * @used-by \Dfr\Core\Console\State::execute()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param string|object $m
 * @param string $type [optional]
 * @return string
 * @throws \InvalidArgumentException
 */
function df_module_dir($m, $type = '') {
	$reader = df_o(Reader::class); /** @var Reader $reader */
	return $reader->getModuleDir($type, df_module_name($m));
}

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
 * @used-by df_currency_nums()
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
	df_module_dir($m, Dir::MODULE_ETC_DIR), $localPath
);}
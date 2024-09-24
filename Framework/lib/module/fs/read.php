<?php
use Closure as F;

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
 * @used-by Dfe\PostFinance\W\Event::optionTitle()
 * @used-by Dfe\Qiwi\API\Validator::codes()
 * @param string|object|null $m
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_module_csv2($m, string $name, $onE = true):array {return df_module_file_read($m, $name, 'csv',
	function(string $f):array {return df_csv_o()->getDataPairs($f);}, $onE
);}

/**
 * 2020-02-02
 * @see df_module_csv2()
 * @see df_module_json()
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by Dfe\Sift\PM\FE::onFormInitialized()
 * @param string|object|null $m
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_module_enum($m, string $name, $onE = true):array {return df_module_file_read($m, $name, 'txt',
	function(string $f):array {return df_explode_n(df_contents($f));}, $onE
);}

/**
 * 2017-09-01
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @used-by df_module_csv2()
 * @used-by df_module_enum()
 * @used-by df_module_json()
 * @param string|object|null $m
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_module_file_read($m, string $name, string $ext, F $parser, $onE = true):array {return dfcf(
	# 2023-07-26
	# 1) The previous code was $onE = true:
	# https://github.com/mage2pro/core/blob/9.9.4/Framework/lib/module/fs/read.php#L57
	# 2) It led to the error:
	# `df_sentry_m()` fails: «`Magento_Framework` is not a module, so it does not have subpaths specific for modules»:
	# https://github.com/mage2pro/core/issues/267
	function($m, string $name, string $ext, F $parser, $onE):array {
		$f = df_module_file_name($m, $name, $ext, $onE);
		return !$f ? [] : $parser($f);
	}, func_get_args()
);}

/**
 * 2017-01-27
 * $m could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * @see df_module_csv2()
 * @see df_module_enum()
 * @used-by df_currency_nums()
 * @used-by df_sentry_m()
 * @used-by vendor/mage2pro/portal-stripe/view/frontend/templates/page/settings.phtml
 * @used-by Df\PaypalClone\W\Event::statusT()
 * @used-by Dfe\AllPay\W\Event::tlByCode()
 * @used-by Dfe\CheckoutCom\Source\Prefill::config()
 * @used-by Dfe\IPay88\Source\Option::all()
 * @used-by Dfe\IPay88\Source\Option::map()
 * @used-by Dfe\YandexKassa\Source\Option::map()
 * @used-by TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @param string|object|null $m
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_module_json($m, string $name, $onE = true):array {return df_module_file_read(
	$m, $name, 'json', function(string $f):array {return df_json_file_read($f);}, $onE
);}
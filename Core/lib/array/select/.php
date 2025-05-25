<?php
/**
 * Раньше функция @see dfa() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы вынес обработку объектов в отдельную функцию @see dfo().
 * 2020-01-29
 * If $k is an array, then the function returns a subset of $a with $k keys in the same order as in $k.
 * We heavily rely on it, e.g., in some payment methods: @see \Dfe\Dragonpay\Signer\Request::values()
 * @see dfac()
 * @see dfad()
 * @see dfaoc()
 * @see dfo()
 * @used-by df_caller_module()
 * @used-by df_bt_entry_file()
 * @used-by df_bt_entry_func()
 * @used-by df_bt_entry_line()
 * @used-by df_bt_has()
 * @used-by df_call()
 * @used-by df_category_dp_meta()
 * @used-by df_ci_get()
 * @used-by df_cli_argv()
 * @used-by df_countries_options()
 * @used-by df_currencies_options()
 * @used-by df_db_name()
 * @used-by df_deployment_cfg()
 * @used-by df_fe_attrs()
 * @used-by df_fe_fc()
 * @used-by df_github_request()
 * @used-by df_log_l()
 * @used-by df_magento_version()
 * @used-by df_module_name_by_path()
 * @used-by df_mvar()
 * @used-by df_oi_get()
 * @used-by df_package()
 * @used-by df_prop()
 * @used-by df_prop_k()
 * @used-by df_request_method()
 * @used-by df_trd()
 * @used-by df_visitor_ip()
 * @used-by dfa_has_keys()
 * @used-by dfa_prepend()
 * @used-by dfa_strict()
 * @used-by dfa_try()
 * @used-by dfac()
 * @used-by dfad()
 * @used-by dfaoc()
 * @used-by Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by Amasty\Checkout\Model\Optimization\LayoutJsDiffProcessor::moveArray(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/120)
 * @used-by Df\Config\Source::options()
 * @used-by Df\Core\Controller\Index\Index::execute()
 * @used-by Df\Core\O::a()
 * @used-by Df\Framework\Form\Element\Fieldset::select()
 * @used-by Df\Framework\Log\Dispatcher::handle()
 * @used-by Df\Framework\Plugin\Css\PreProcessor\File\FileList\Collator::afterCollate()
 * @used-by Df\Framework\Request::extra()
 * @used-by Dfe\FacebookLogin\Customer::responseJson()
 * @used-by Df\Payment\Charge::metadata()
 * @used-by Dfe\GoogleFont\ResponseValidator::short()
 * @used-by Dfe\GoogleFont\Font\Variant\Preview\Params::fromRequest()
 * @used-by Dfe\GoogleFont\Fonts::get()
 * @used-by Dfe\GoogleFont\Fonts::responseA()
 * @used-by Df\Payment\W\Reader::r()
 * @used-by Df\Payment\W\Reader::test()
 * @used-by Df\PaypalClone\Signer::v()
 * @used-by Df\Sentry\Client::__construct()
 * @used-by Df\Sentry\Client::capture()
 * @used-by Df\Sentry\Client::get_http_data()
 * @used-by Df\Sso\CustomerReturn::mc()
 * @used-by Df\Xml\G::importArray()
 * @used-by Dfe\AlphaCommerceHub\W\Event::providerRespL()
 * @used-by Dfe\AmazonLogin\Customer::res()
 * @used-by Dfe\CheckoutCom\Source\Prefill::config()
 * @used-by Dfe\CurrencyFormat\O::postProcess()
 * @used-by Dfe\TwoCheckout\Handler\RefundIssued::process()
 * @used-by Dfe\TwoCheckout\Method::charge()
 * @used-by Dfe\ZoomVe\OriginCityLocator::d() (https://github.com/mage2pro/zoom-ve/issues/7)
 * @used-by DxMoto\Core\Observer\CanLog::execute()
 * @used-by Mageside\CanadaPostShipping\Model\Carrier::_doRatesRequest() (canadasatellite.ca)
 * @used-by Mangoit\MediaclipHub\Helper\Data::getMediaClipProjects()
 * @used-by TFC\Core\Observer\CanLog::execute()
 * @used-by TFC\GoogleShopping\Att\Brand::v() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/8)
 * @used-by VegAndTheCity\Core\Plugin\Mageplaza\Search\Helper\Data::afterGetProducts()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs.phtml (https://github.com/cabinetsbay/catalog/issues/9)
 * @param array(int|string => mixed) $a
 * @param string|string[]|int|null $k
 * @param mixed|callable $d
 * @return mixed|null|array(string => mixed)
 */
function dfa(array $a, $k, $d = null) {return
	# 2016-02-13
	# Нельзя здесь писать `return df_if2(isset($array[$k]), $array[$k], $d);`, потому что получим «Notice: Undefined index».
	# 2016-08-07
	# В Closure мы можем безнаказанно передавать параметры, даже если closure их не поддерживает https://3v4l.org/9Sf7n
	df_nes($k) ? $a : (is_array($k)
		/**
		 * 2022-11-26
		 * Added `!$k`.
		 * @see df_arg() relies on it if its argument is an empty array:
		 *		df_arg([]) => []
		 *		dfa($a, df_arg([])) => $a
		 * https://3v4l.org/C09vn
		 */
		? (!$k ? $a : dfa_select_ordered($a, $k))
		: (isset($a[$k]) ? $a[$k] : (df_contains($k, '/') ? dfa_deep($a, $k, $d) : df_call_if($d, $k)))
	)
;}
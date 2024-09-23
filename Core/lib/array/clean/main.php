<?php
use Df\Core\Exception as DFE;
use Magento\Framework\DataObject as _DO;

/**
 * 2015-02-07
 * Обратите внимание,
 * что во многих случаях эффективней использовавать @see array_filter() вместо @see df_clean().
 * https://php.net/manual/function.array-filter.php
 * @see array_filter() с единственным параметром удалит из массива все элементы,
 * чьи значения приводятся к логическому «false».
 * Т.е., помимо наших array('', null, []),
 * @see array_filter() будет удалять из массива также элементы со значениями «false» и «0».
 * Если это соответствует требуемому поведению в конретной точке программного кода,
 * то используйте именно @see array_filter(),
 * потому что встроенная функция @see array_filter() в силу реализации на языке С
 * будет работать на порядки быстрее, нежели @see df_clean().
 * 2015-01-22 Теперь из исходного массива будут удаляться элементы, чьим значением является пустой массив.
 * 2016-11-22
 * К сожалению, короткое решение array_diff($a, array_merge(['', null, []], df_args($remove)))
 * приводит к сбою: «Array to string conversion» в случае многомерности одного из аргументов:
 * http://stackoverflow.com/questions/19830585
 * У нас такая многомерность имеется всегда в связи с ['', null, []].
 * Поэтому вынуждены использовать ручную реализацию.
 * В то же время и предудущая (использованная годами) реализация слишком громоздка:
 * https://github.com/mage2pro/core/blob/1.9.14/Core/lib/array.php?ts=4#L31-L54
 * Современная версия интерпретатора PHP позволяет её сократить.
 * 2017-02-13 Добавил в список удаления «false».
 * @see df_clean_null()
 * @see df_clean_r()
 * @used-by df_cc_class()
 * @used-by df_cc_path()
 * @used-by df_ccc()
 * @used-by df_clean_xml()
 * @used-by df_db_or()
 * @used-by df_explode_space() (https://github.com/mage2pro/core/issues/422)
 * @used-by df_fe_name_short()
 * @used-by df_http_get()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_oro_get_list()
 * @used-by df_page_result()
 * @used-by df_store_code_from_url()
 * @used-by df_zf_http_last_req() 
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
 * @used-by \Dfe\GingerPaymentsBase\Charge::pCustomer()
 * @used-by \Dfe\GingerPaymentsBase\Test\CreateOrder::t01_success()
 * @used-by \Df\OAuth\App::pCommon()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Df\Payment\Block\Info::rPDF()
 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
 * @used-by \Df\Payment\Method::iiaSetTRR()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Df\Sentry\Client::capture()
 * @used-by \Df\Sentry\Client::send()
 * @used-by \Df\Sso\Button\Js::attributes()
 * @used-by \Df\Sso\CustomerReturn::customerData()
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\Address\AbstractAddress::afterValidate()
 * @used-by \Dfe\Dynamics365\API\Facade::productpricelevels()
 * @used-by \Dfe\Dynamics365\Button::pExtra()
 * @used-by \Dfe\FacebookLogin\Customer::req()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge::kl_order_lines()
 * @used-by \Dfe\Markdown\Modifier::modifyData()
 * @used-by \Dfe\Moip\API\Validator::long()
 * @used-by \Dfe\Moip\P\Reg::p()
 * @used-by \Dfe\Moip\Test\Card::card()
 * @used-by \Dfe\Moip\Test\CaseT\Customer::pCustomer()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @used-by \Dfe\Salesforce\Button::pExtra()
 * @used-by \Dfe\Sift\Payload\Promotions::p()
 * @used-by \Dfe\Stripe\Facade\Charge::refund()
 * @used-by \Dfe\Stripe\Facade\Charge::refundMeta()
 * @used-by \Dfe\Stripe\P\Reg::p()
 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
 * @used-by \Dfe\TwoCheckout\Exception::messageC()
 * @used-by \Dfe\TwoCheckout\LineItem::build()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::build()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
 * @used-by \KingPalm\B2B\Block\Registration::text()
 * @used-by \SpryngPaymentsApiPhp\Controller\TransactionController::refund()
 * @used-by \Stock2Shop\OrderExport\Payload::visitor()
 * @used-by \TFC\Core\B\Home\Slider::i()
 * @param mixed ...$k [optional]
 */
function df_clean(array $r, ...$k):array {/** @var mixed[] $r */return df_clean_r(
	$r, array_merge([false], df_args($k)), false
);}

/**
 * 2017-02-18
 * https://3v4l.org/l2b4m
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\StripeClone\P\Charge::request()
 * @used-by \Df\StripeClone\P\Reg::request()
 * @used-by \Dfe\Qiwi\Signer::sign()
 * @param array(int|string => mixed) $a
 * @param mixed ...$remove [optional]
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_clean_keys(array $a, ...$remove):array {
	# 2017-02-18
	# Для неассоциативных массивов функция не только не имеет смысла,
	# но и работала бы некорректно в свете замечания к функции df_clean():
	# тот алгоритм, который мы там используем для устранения дыр в массиве-результате,
	# здесь привёл бы к полной утрате ключей.
	df_assert_assoc($a);
	$remove = array_merge(['', null], df_args($remove));
	return array_filter($a, function($k) use($remove) {return !in_array($k, $remove, true);}, ARRAY_FILTER_USE_KEY);
}

/**
 * 2023-07-20
 * @see df_clean()
 * @see df_clean_r()
 * @used-by dfa_select_ordered()
 */
function df_clean_null(array $r):array {return array_filter($r, function($v) {return !is_null($v);});}

/**
 * 2020-02-05
 * @see df_clean()
 * @see df_clean_null()
 * 1) It works recursively.
 * 2) I does not remove `false`.
 * @used-by df_clean()
 * @used-by df_clean_r()
 * @used-by df_xml_atts
 * @used-by \Df\Core\Html\Tag::__construct()
 * @used-by \Dfe\Sift\API\Client::_construct()
 */
function df_clean_r(array $r, array $k = [], bool $req = true):array {/** @var mixed[] $r */
	/** 2020-02-05 @see array_unique() does not work correctly here, even with the @see SORT_REGULAR flag. */
	$k = array_merge($k, ['', null, []]);
	if ($req) {
		$r = df_map($r, function($v) use($k) {return !is_array($v) ? $v : df_clean_r($v, $k);});
	}
	return df_filter($r, function($v) use($k):bool {return !in_array($v, $k, true);});
}

/**
 * Отличается от @see df_clean() дополнительным удалением их исходного массива элементов,
 * чьим значением является применение @see df_cdata() к пустой строке.
 * Пример применения:
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getElement_Производитель()
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function df_clean_xml(array $a):array {return df_clean($a, [df_cdata('')]);}

/**
 * 2018-08-11
 * @used-by dfa_remove_objects()
 * @used-by \Stock2Shop\OrderExport\Payload::address()
 * @used-by \Stock2Shop\OrderExport\Payload::get()
 * @used-by \Stock2Shop\OrderExport\Payload::payment()
 * @param _DO|mixed[] $v
 * @return mixed
 */
function dfa_remove_objects($v, bool $clean = true) {
	$r = array_filter(is_array($v) ? $v : df_gd($v), function($v) {return
		is_object($v) ? false : (!is_array($v) ? true : dfa_remove_objects($v))
	;});
	return !$clean ? $r : df_clean($r);
}
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
 *
 * 2015-01-22 Теперь из исходного массива будут удаляться элементы, чьим значением является пустой массив.
 *
 * 2016-11-22
 * К сожалению, короткое решение array_diff($a, array_merge(['', null, []], df_args($remove)))
 * приводит к сбою: «Array to string conversion» в случае многомерности одного из аргументов:
 * http://stackoverflow.com/questions/19830585
 * У нас такая многомерность имеется всегда в связи с ['', null, []].
 * Поэтому вынуждены использовать ручную реализацию.
 * В то же время и предудущая (использованная годами) реализация слишком громоздка:
 * https://github.com/mage2pro/core/blob/1.9.14/Core/lib/array.php?ts=4#L31-L54
 * Современная версия интерпретатора PHP позволяет её сократить.
 *
 * 2017-02-13 Добавил в список удаления «false».
 *
 * @see df_clean_r()
 * @used-by df_cc_class()
 * @used-by df_ccc()
 * @used-by df_clean_xml()
 * @used-by df_db_or()
 * @used-by df_fe_name_short()
 * @used-by df_http_get()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_oro_get_list()
 * @used-by df_page_result()
 * @used-by df_store_code_from_url()
 * @used-by df_zf_http_last_req() 
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @used-by \Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
 * @used-by \Df\GingerPaymentsBase\Test\CreateOrder::t01_success()
 * @used-by \Df\OAuth\App::pCommon()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Df\Payment\Block\Info::rPDF()
 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
 * @used-by \Df\Payment\Method::iiaSetTRR()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Df\Qa\Context::base()
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
 * @used-by \Dfe\FacebookLogin\Customer::request()
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
 * @param mixed[] $r
 * @param mixed ...$k [optional]
 * @return mixed[]
 */
function df_clean(array $r, ...$k):array {/** @var mixed[] $r */return df_clean_r(
	$r, array_merge([false], df_args($k)), false
);}

/**
 * 2020-02-05
 * @see df_clean()
 * 1) It works recursively.
 * 2) I does not remove `false`.
 * @used-by df_clean()
 * @used-by df_clean_r()
 * @used-by \Dfe\Sift\API\Client::_construct()
 * @param mixed[] $r
 * @param mixed[] $k
 * @return mixed[]
 */
function df_clean_r(array $r, $k = [], bool $req = true):array {/** @var mixed[] $r */
	/** 2020-02-05 @see array_unique() does not work correctly here, even with the @see SORT_REGULAR flag. */
	$k = array_merge($k, ['', null, []]);
	if ($req) {
		$r = df_map($r, function($v) use($k) {return !is_array($v) ? $v : df_clean_r($v, $k);});
	}
	return df_filter($r, function($v) use($k) {return !in_array($v, $k, true);});
}

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
 * Отличается от @see df_clean() дополнительным удалением их исходного массива элементов,
 * чьим значением является применение @see df_cdata() к пустой строке.
 * Пример применения:
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getElement_Производитель()
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function df_clean_xml(array $a):array {return df_clean($a, [df_cdata('')]);}

/**
 * 2016-11-08
 * Отличия этой функции от @uses array_filter():
 * 1) работает не только с массивами, но и с @see \Traversable
 * 2) принимает аргументы в произвольном порядке.
 * Третий параметр — $flag — намеренно не реализовал,
 * потому что вроде бы для @see \Traversable он особого смысла не имеет,
 * а если у нас гарантирвоанно не @see \Traversable, а ассоциативный массив,
 * то мы можем использовать array_filter вместо df_filter.
 * 2020-02-05 Now it correcly handles non-associative arrays.
 * @used-by df_clean_r()
 * @used-by \Frugue\Core\Plugin\Sales\Model\Quote::afterGetAddressesCollection()
 * @used-by \TFC\Core\Plugin\Sales\Model\Order::afterGetParentItemsRandomCollection()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_filter($a1, $a2):array { /** @var array $r */
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($a, $f) = dfaf($a1, $a2); /** @var array|\Traversable $a */ /** @var callable $f */
	$a = df_ita($a);
	$r = array_filter(df_ita($a), $f);
	/**
	 * 2017-02-16
	 * Если исходный массив был неассоциативным, то после удаления из него элементов в индексах будут бреши.
	 * Это может приводить к неприятным последствиям:
	 * 1) @see df_is_assoc() для такого массива уже будет возвращать false, а не true, как для входного массива.
	 * 2) @see df_json_encode() будет кодировать такой массив как объект, а не как массив,
	 * что может привести (и приводит, например, у 2Checkout) к сбоям различных API
	 * 3) Последующие алгоритмы, считающие, что массив — неассоциативный, могут работать сбойно.
	 * По всем этим причинам привожу результат к неассоциативному виду, если исходный массив был неассоциативным.
	 */
	return df_is_assoc($a) ? $r : array_values($r);
}

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
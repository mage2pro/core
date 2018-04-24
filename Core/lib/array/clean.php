<?php
use Df\Core\Exception as DFE;

/**
 * 2015-02-07
 * Обратите внимание,
 * что во многих случаях эффективней использовавать @see array_filter() вместо @see df_clean().
 * http://php.net/manual/function.array-filter.php
 * @see array_filter() с единственным параметром удалит из массива все элементы,
 * чьи значения приводятся к логическому «false».
 * Т.е., помимо наших array('', null, []),
 * @see array_filter() будет удалять из массива также элементы со значениями «false» и «0».
 * Если это соответствует требуемому поведению в конретной точке программного кода,
 * то используйте именно @see array_filter(),
 * потому что встроенная функция @see array_filter() в силу реализации на языке С
 * будет работать на порядки быстрее, нежели @see df_clean().
 *
 * 2015-01-22
 * Теперь из исходного массива будут удаляться элементы, чьим значением является пустой массив.
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
 * 2017-02-13
 * Добавил в список удаления «false».
 *
 * @used-by df_cc_class()
 * @used-by df_cc_kv()
 * @used-by df_ccc()
 * @used-by df_clean_xml()
 * @used-by df_db_or()
 * @used-by df_fe_name_short()
 * @used-by df_http_get()
 * @used-by df_oro_get_list()
 * @used-by df_page_result()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Core\Helper\Text::parseTextarea()
 * @used-by \Df\Framework\Plugin\Reflection\DataObjectProcessor::aroundBuildOutputDataArray()
 * @used-by \Df\GingerPaymentsBase\Charge::pCustomer()
 * @used-by \Df\GingerPaymentsBase\T\CreateOrder::t01_success()
 * @used-by \Df\OAuth\App::pCommon()
 * @used-by \Df\OAuth\FE\Button::onFormInitialized()
 * @used-by \Df\Payment\Block\Info::rPDF()
 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
 * @used-by \Df\Payment\Method::iiaSetTRR()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Df\Sentry\Client::capture()
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
 * @used-by \Dfe\Moip\T\Card::card()
 * @used-by \Dfe\Moip\T\CaseT\Customer::pCustomer()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @used-by \Dfe\Salesforce\Button::pExtra()
 * @used-by \Dfe\Stripe\Facade\Charge::refund()
 * @used-by \Dfe\Stripe\Facade\Charge::refundMeta()
 * @used-by \Dfe\Stripe\P\Reg::p()
 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
 * @used-by \Dfe\TwoCheckout\Exception::messageC()
 * @used-by \Dfe\TwoCheckout\LineItem::build()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::build()
 * @used-by \SpryngPaymentsApiPhp\Controller\TransactionController::refund()
 *
 * @param mixed[] $a
 * @param mixed[] $remove [optional]
 * @return mixed[]
 */
function df_clean(array $a, ...$remove) {
	$remove = array_merge(['', null, [], false], df_args($remove));
	/** @var mixed[] $result */
	$result = array_filter($a, function($v) use($remove) {return !in_array($v, $remove, true);});
	/**
	 * 2017-02-16
	 * Если исходный массив был неассоциативным,
	 * то после удаления из него элементов в индексах будут бреши.
	 * Это может приводить к неприятным последствиям:
	 * 1) @see df_is_assoc() для такого массива уже будет возвращать false,
	 * а не true, как для входного массива.
	 * 2) @see df_json_encode() будет кодировать такой массив как объект, а не как массив,
	 * что может привести (и приводит, например, у 2Checkout) к сбоям различных API
	 * 3) Последующие алгоритмы, считающие, что массив — неассоциативный, могут работать сбойно.
	 * По всем этим причинам привожу результат к неассоциативному виду,
	 * если исходный массив был неассоциативным.
	 */
	return df_is_assoc($a) ? $result : array_values($result);
}

/**
 * 2017-02-18
 * https://3v4l.org/l2b4m
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\StripeClone\P\Charge::request()
 * @used-by \Df\StripeClone\P\Reg::request()
 * @used-by \Dfe\Qiwi\Signer::sign()
 * @param array(int|string => mixed) $a
 * @param mixed[] $remove [optional]
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_clean_keys(array $a, ...$remove) {
	// 2017-02-18
	// Для неассоциативных массивов функция не только не имеет смысла,
	// но и работала бы некорректно в свете замечания к функции df_clean():
	// тот алгоритм, который мы там используем для устранения дыр в массиве-результате,
	// здесь привёл бы к полной утрате ключей.
	df_assert_assoc($a);
	$remove = array_merge(['', null], df_args($remove));
	/** @var mixed[] $result */
	return array_filter($a, function($k) use($remove) {return
		!in_array($k, $remove, true)
	;}, ARRAY_FILTER_USE_KEY);
}

/**
 * Отличается от @see df_clean() дополнительным удалением их исходного массива элементов,
 * чьим значением является применение @see df_cdata() к пустой строке.
 * Пример применения:
 * @used-by Df_1C_Cml2_Export_Processor_Catalog_Product::getElement_Производитель()
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function df_clean_xml(array $a) {return df_clean($a, [df_cdata('')]);}
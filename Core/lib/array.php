<?php
use Df\Core\A;
use Magento\Framework\DataObject;

/**
 * @param mixed|mixed[] $v
 * @return mixed[]|string[]|float[]|int[]
 */
function df_array($v) {return is_array($v) ? $v : [$v];}

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
 * @used-by \Df\Payment\Operation\Source::addressMixed()
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

/**
 * 2015-02-11
 * Аналог @see array_column() для коллекций.
 * Ещё один аналог: @see \Magento\Framework\Data\Collection::getColumnValues(),
 * но его результат — не ассоциативный.
 *
 * 2016-07-31
 * При вызове с 2-мя параметрами эта функция идентична функции @see df_each()
 *
 * 2017-07-09
 * Now the function accepts an array as $object.
 * Even in this case it differs from @see array_column():
 * array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 *
 * @param \Traversable|array(int|string => DataObject|array(string => mixed)) $c
 * @param string|\Closure $fv
 * @param string|null $fk [optional]
 * @return array(int|string => mixed)
 */
function df_column($c, $fv, $fk = null) {return df_map_kr($c, function($k, $v) use($fv, $fk) {return [
	!$fk ? $k : df_call($v, $fk), df_call($v, $fv)
];});}

/**
 * 2015-02-07
 * Эта функция аналогична методу @see \Magento\Framework\Data\Collection::walk(),
 * и даже может использоваться вместо @see \Magento\Framework\Data\Collection::walk(),
 * однако, в отличие от @see \Magento\Framework\Data\Collection::walk(),
 * она способна работать не только с коллекцией,
 * но также с массивом объектов и объектом, поддерживающим интерфейс @see \Traversable.
 *
 * 2016-07-31
 * При вызове с 2-мя параметрами эта функция идентична функции @see df_column()
 *
 * 2017-07-09
 * Now the function accepts an array as $object.
 * Even in this case it differs from @see array_column():
 * array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 *
 * @used-by \Df\Qa\Context::render()
 *
 * @param \Traversable|array(int|string => DataObject|array(string => mixed)) $c
 * @param string|callable $f
 * @param mixed ...$p
 * @return mixed[]
 */
function df_each($c, $f, ...$p) {return df_map(function($v) use($f, $p) {return df_call($v, $f, $p);}, $c);}

/**
 * 2015-02-18
 * По смыслу функция @see df_extend() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see df_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see df_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартные рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * http://php.net/manual/function.array-merge-recursive.php
 * Наша функция df_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * http://php.net/manual/function.array-replace-recursive.php
 * Наша функция df_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 */
function df_extend(array $defaults, array $newValues) {
	/** @var array(string => mixed) $result */
	// Здесь ошибочно было бы $result = [],
	// потому что если ключ отсутствует в $newValues,
	// то тогда он не попадёт в $result.
	$result = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */
		/** @var mixed $newValue */
		/** @var mixed $defaultValue */
		$defaultValue = dfa($defaults, $key);
		if (!is_array($defaultValue)) {
			// 2016-08-23
			// unset добавил сегодня.
			if (is_null($newValue)) {
				unset($result[$key]);
			}
			else {
				$result[$key] = $newValue;
			}
		}
		else {
			if (is_array($newValue)) {
				$result[$key] = df_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($result[$key]);
				}
				else {
					// Если значение по умолчанию является массивом,
					// а новое значение не является массивом,
					// то это наверняка говорит об ошибке программиста.
					df_error(
						"df_extend: значением по умолчанию ключа «{$key}» является массив {defaultValue},"
						. "\nоднако программист ошибочно пытается заместить его"
						. ' значением {newValue} типа «{newType}», что недопустимо:'
						. "\nзамещаемое значение для массива должно быть либо массивом, либо «null»."
						,[
							'{defaultValue}' => df_t()->singleLine(df_dump($defaultValue))
							,'{newType}' => gettype($newValue)
							,'{newValue}' => df_dump($newValue)
						]
					);
				}
			}
		}
	}
	return $result;
}

/**
 * 2016-11-08
 * Отличия этой функции от @uses array_filter():
 * 1) работает не только с массивами, но и с @see \Traversable
 * 2) принимает аргументы в произвольном порядке.
 * Третий параметр — $flag — намеренно не реализовал,
 * потому что вроде бы для @see \Traversable он особого смысла не имеет,
 * а если у нас гарантирвоанно не @see \Traversable, а ассоциативный массив,
 * то мы можем использовать array_filter вместо df_filter.
 * @param callable|array(int|string => mixed)|array[]\Traversable $a
 * @param callable|array(int|string => mixed)|array[]|\Traversable $b
 * @return array(int|string => mixed)
 */
function df_filter($a, $b) {return array_filter(...(
	is_callable($a) ? [df_ita($b), $a] : [df_ita($a), $b]
));}

/**
 * 2016-10-25 Оказалось, что в ядре нет такой функции.
 * @used-by df_handle_prefix()
 * @used-by df_oq_sa()
 * @used-by df_sales_email_sending()
 * @used-by \Df\Framework\Plugin\View\Layout::afterIsCacheable()
 * @used-by \Df\Payment\Info\Report::addAfter()
 * @used-by \Df\Payment\Method::amountFactor()
 * @used-by \Df\Payment\TM::confirmed()
 * @used-by \Dfe\Stripe\Method::cardType()
 * @param callable|array(int|string => mixed)|array[]|mixed|\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|mixed|\Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|null
 */
function df_find($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/** @var callable $callback */  /** @var array(int|string => mixed)|\Traversable $array */
	list($array, $callback) = dfaf($a1, $a2);
	df_assert_callable($callback);
	$array = df_ita(df_assert_traversable($array));
	$pAppend = df_array($pAppend);
	$pPrepend = df_array($pPrepend);
	$result = null; /** @var mixed|null $result */
	foreach ($array as $key => $item) {
		/** @var int|string $key */
		/** @var mixed $item */
		/** @var mixed[] $primaryArgument */
		switch ($keyPosition) {
			case DF_BEFORE:
				$primaryArgument = [$key, $item];
				break;
			case DF_AFTER:
				$primaryArgument = [$item, $key];
				break;
			default:
				$primaryArgument = [$item];
		}
		if ($r = call_user_func_array($callback, array_merge($pPrepend, $primaryArgument, $pAppend))) {
			$result = !is_bool($r) ? $r : $item;
			break;
		}
	}
	return $result;
}

/**
 * Функция возвращает null, если массив пуст.
 * Обратите внимание, что неверен код
 *	$result = reset($a);
 *	return (false === $result) ? null : $result;
 * потому что если @uses reset() вернуло false, это не всегда означает сбой метода:
 * ведь первый элемент массива может быть равен false.
 * @see df_last()
 * @see df_tail()
 * @param array $a
 * @return mixed|null
 */
function df_first(array $a) {return !$a ? null : reset($a);}

/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see df_tail() отсекает первый элемент массива.
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_head(array $a) {return array_slice($a, 0, -1);}

/**
 * 2015-12-30
 * Преобразует коллекцию или массив в карту.
 * @param string|\Closure $method
 * @param \Traversable|array(int|string => DataObject) $items
 * @return mixed[]
 */
function df_index($method, $items) {return array_combine(df_column($items, $method), $items);}

/**
 * 2015-02-07
 * Обратите внимание, что алгоритмов проверки массива на ассоциативность найдено очень много:
 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
 * Я уже давно (несоколько лет) использую приведённый ниже.
 * Пока он меня устраивает, да и сама задача такой проверки
 * возникает у меня в Российской сборке Magento редко
 * и не замечал её особого влияния на производительность системы.
 * Возможно, другие алгоритмы лучше, лень разбираться.
 * 2017-10-29 It returns `true` for an empty array.
 * @used-by df_assert_assoc()
 * @used-by df_call()
 * @used-by df_clean()
 * @used-by df_json_sort()
 * @used-by df_ksort_r_ci()
 * @used-by df_sort()
 * @used-by dfa_insert()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Payment\ConfigProvider::configOptions()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Xml\X::importArray()
 * @param array(int|string => mixed) $a
 * @return bool
 */
function df_is_assoc(array $a) {
	if (!($result = !$a)) { /** @var bool $result */
		foreach (array_keys($a) as $k => $v) {
			// 2015-02-07
			// Согласно спецификации PHP, ключами массива могут быть целые числа, либо строки.
			// Третьего не дано.
			// http://php.net/manual/language.types.array.php
			// 2017-02-18
			// На самом деле ключом может быть и null, что неявно приводится к пустой строке:
			// http://stackoverflow.com/a/18247435
			// 2015-02-07
			// Раньше тут стояло !is_int($key)
			// Способ проверки $key !== $value нашёл по ссылке ниже:
			// http://www.php.net/manual/en/function.is-array.php#84488
			if ($k !== $v) {
				$result = true;
				break;
			}
		}
	}
	return $result;
}

/**
 * 2015-04-17
 * Проверяет, является ли массив многомерным.
 * http://stackoverflow.com/a/145348
 * Пока никем не используется.
 * @param array(int|string => mixed) $a
 * @return bool
 */
function df_is_multi(array $a) {
	/** @var bool $result */
	$result = false;
	foreach ($a as $v) {
		/** @var mixed $v */
		if (is_array($v)) {
			$result = true;
			break;
		}
	}
	return $result;
}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $t
 * @return array
 */
function df_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}

/**
 * 2016-01-29
 * @see df_sort()
 * @used-by df_ksort_r()
 * @used-by df_stores()
 * @used-by df_trd_set()
 * @used-by \Dfe\Dynamics365\API\Facade::p()
 * @used-by \Dfe\Qiwi\Signer::sign()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfr\Core\Console\Update::execute()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort(array $a, $f = null) {$f ? uksort($a, $f) : ksort($a); return $a;}

/**
 * 2017-08-22
 * Note 1. For now it is never used.
 * Note 2. An alternative implementation: df_ksort($a, 'strcasecmp')
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then ksort($a, SORT_FLAG_CASE|SORT_STRING) will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @see df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_ci(array $a) {ksort($a, SORT_FLAG_CASE|SORT_STRING); return $a;}

/**
 * 2017-07-05
 * 2017-08-22 From now it is never used. @see df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort_r(array $a, $f = null) {return df_ksort(df_map_k(function($k, $v) use($f) {return
	!is_array($v) ? $v : df_ksort_r($v, $f)
;}, $a), $f);}

/**
 * 2017-08-22
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then df_ksort_r($a, 'strcasecmp') will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_sort()
 * @uses df_ksort_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_r_ci(array $a) {return !df_is_assoc($a) ? $a : df_ksort_r($a, 'strcasecmp');}

// Глобальные константы появились в PHP 5.3.
// http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
const DF_AFTER = 1;
const DF_BEFORE = -1;

/**
 * 2015-02-11
 * Эта функция аналогична @see array_map(), но обладает 3-мя дополнительными возможностями:
 * 1) её можно применять не только к массивам, но и к @see \Traversable.
 * 2) она позволяет удобным способом передавать в $callback дополнительные параметры
 * 3) позволяет передавать в $callback ключи массива
 * до и после основного параметра (элемента массива).
 * 4) позволяет в результате использовать нестандартные ключи
 * Обратите внимание, что
 *		df_map('Df_Cms_Model_ContentsMenu_Applicator::i', $this->getCmsRootNodes())
 * эквивалентно
 *		$this->getCmsRootNodes()->walk('Df_Cms_Model_ContentsMenu_Applicator::i')
 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @param bool $returnKey [optional]
 * @return array(int|string => mixed)
 */
function df_map($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0, $returnKey = false) {
	/** @var callable $callback */
	/** @var array(int|string => mixed)|\Traversable $array */
	list($array, $callback) = dfaf($a1, $a2);
	df_assert_callable($callback);
	$array = df_ita(df_assert_traversable($array));
	/** @var array(int|string => mixed) $result */
	if (!$pAppend && !$pPrepend && 0 === $keyPosition && !$returnKey) {
		$result = array_map($callback, $array);
	}
	else {
		$pAppend = df_array($pAppend);
		$pPrepend = df_array($pPrepend);
		$result = [];
		foreach ($array as $key => $item) {
			/** @var int|string $key */
			/** @var mixed $item */
			/** @var mixed[] $primaryArgument */
			switch ($keyPosition) {
				case DF_BEFORE:
					$primaryArgument = [$key, $item];
					break;
				case DF_AFTER:
					$primaryArgument = [$item, $key];
					break;
				default:
					$primaryArgument = [$item];
			}
			/** @var mixed[] $arguments */
			$arguments = array_merge($pPrepend, $primaryArgument, $pAppend);
			/** @var mixed $item */
			$item = call_user_func_array($callback, $arguments);
			if (!$returnKey) {
				$result[$key] = $item;
			}
			else {
				// 2016-10-25
				// Позволяет возвращать нестандартные ключи.
				$result[$item[0]] = $item[1];
			}
		}
	}
	return $result;
}

/**
 * 2016-08-09
 * Функция принимает аргументы в любом порядке.
 * @used-by df_cc_kv()
 * @used-by dfe_modules_log()
 * @used-by \Df\Payment\ConfigProvider::configOptions()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_k($a1, $a2) {return df_map($a1, $a2, [], [], DF_BEFORE);}

/**
 * 2016-11-08
 * Функция принимает аргументы в любом порядке.
 * @see dfa_key_transform()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_kr($a1, $a2) {return df_map($a1, $a2, [], [], DF_BEFORE, true);}

/**
 * 2016-11-08
 * Функция принимает аргументы в любом порядке.
 * @used-by df_modules_my()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function df_map_r($a1, $a2) {return df_map($a1, $a2, [], [], 0, true);}

/**
 * Оба входных массива должны быть ассоциативными
 * @param array(string => mixed) $array1
 * @param array(string => mixed) $array2
 * @return array(string => mixed)
 */
function df_merge_not_empty(array $array1, array $array2) {return array_filter($array2) + $array1;}

/**
 * 2015-02-11
 * Эта функция отличается от @see array_merge() только тем,
 * что все вместо нескольких параметров принимает массив из параметров.
 * Это бывает удобно в функциональном программировании, например:
 * @used-by Df_Dataflow_Model_Registry_MultiCollection::getEntities()
 * @used-by Df_Dellin_Model_Request_Rate::getDates()
 * @param array(array(int|string => mixed)) $arrays
 * @return array(int|string => mixed)
 */
function df_merge_single(array $arrays) {return array_merge(...$arrays); }

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 	$result = end($array);
 	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @see df_first()
 * @see df_tail()
 * @param mixed[] $array
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}

/**
 * 2016-07-18
 * 2016-08-10
 * С сегодняшнего дня я использую функцию @see df_caller_f(),
 * которая, в свою очередь, использует @debug_backtrace()
 * Это приводит к сбою: «Warning: usort(): Array was modified by the user comparison function».
 * http://stackoverflow.com/questions/3235387
 * https://bugs.php.net/bug.php?id=50688
 * По этой причине добавил собаку.
 * @see df_ksort()
 * @used-by df_json_sort()
 * @used-by df_sort_names()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Payment\Info\Report::sort()
 * @used-by \Df\Payment\TM::tResponses()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param array(int|string => mixed) $a
 * @param \Closure|string|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_sort(array $a, $f = null) {
	$isAssoc = df_is_assoc($a); /** @var bool $isAssoc */
	if (!$f) {
		$isAssoc ? asort($a) : sort($a);
	}
	else {
		if (!$f instanceof \Closure) {
			/** @var string $m */
			/** @uses \Magento\Framework\Model\AbstractModel::getId() */
			$m = $f ?: 'getId';
			$f = function($a, $b) use($m) {return !is_object($a) ? $a - $b : $a->$m() - $b->$m();};
		}
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$isAssoc ? @uasort($a, $f) : @usort($a, $f);
	}
	return $a;
}

/**
 * 2017-02-02
 * http://stackoverflow.com/a/7930575
 * @used-by df_modules_p()
 * @used-by df_oqi_leafs()
 * @used-by df_zf_http_last_req()
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Dfe\AlphaCommerceHub\Source\Option::map()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param string[]|mixed[] $a
 * @param string|null $locale
 * @param callable|null $get
 * @return string[]|mixed[]
 */
function df_sort_names(array $a, $locale = null, callable $get = null) {
	$c = new \Collator($locale); /** @var \Collator $c */
	return df_sort($a, function($a, $b) use($c, $get) {return $c->compare(
		!$get ? $a : $get($a), !$get ? $b : $get($b)
	);});
}

/**
 * @used-by Df_InTime_Api::call()
 * http://stackoverflow.com/a/18576902
 * @param mixed $value
 * @return array
 */
function df_stdclass_to_array($value) {return df_json_decode(json_encode($value));}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов,
 * то функция вернёт пустой массив.
 * @see df_first()
 * @see df_last()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_tail(array $a) {return array_slice($a, 1);}

/**
 * http://en.wikipedia.org/wiki/Tuple
 * @param array $arrays
 * @return array
 */
function df_tuple(array $arrays) {
	/** @var array $result */
	$result = [];
	/** @var int $count */
	$countItems = max(array_map('count', $arrays));
	for ($ordering = 0; $ordering < $countItems; $ordering++) {
		/** @var array $item */
		$item = [];
		foreach ($arrays as $arrayName => $array) {
			$item[$arrayName]= dfa($array, $ordering);
		}
		$result[$ordering] = $item;
	}
	return $result;
}

/**
 * Раньше функция @see dfa() была универсальной:
 * она принимала в качестве аргумента $entity как массивы, так и объекты.
 * В 99.9% случаев в качестве параметра передавался массив.
 * Поэтому ради ускорения работы системы
 * вынес обработку объектов в отдельную функцию @see dfo()
 * @param array(int|string => mixed) $a
 * @param string|string[]|int $k
 * @param mixed|callable $d
 * @return mixed|null|array(string => mixed)
 */
function dfa(array $a, $k, $d = null) {return
	// 2016-02-13
	// Нельзя здесь писать return df_if2(isset($array[$k]), $array[$k], $d);
	// потому что получим «Notice: Undefined index».
	//
	// 2016-08-07
	// В \Closure мы можем безнаказанно передавать параметры,
	// даже если closure их не поддерживает https://3v4l.org/9Sf7n
	is_array($k) ? dfa_select_ordered($a, $k) : (isset($a[$k]) ? $a[$k] : df_call_if($d, $k))
;}

/**
 * 2017-02-18
 * [array|callable, array|callable] => [array, callable]
 * @used-by df_find()
 * @used-by dfa_key_transform()
 * @used-by df_map()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a
 * @param null|callable|array(int|string => mixed)|array[]|\Traversable $b [optional]
 * @return array(int|string => mixed)
 */
function dfaf($a, $b) {return is_callable($a) ? [$b, $a] : [$a, $b];}

/**
 * 2017-01-01
 * 2017-01-02
 * 1) Если второй параметр — Closure, то первый должен быть объектом.
 * 2) Возможны ситуации, когда Closure — первый параметр:
 * так происходит при вызове dfak() из статического метода: @used-by \Df\Framework\Request::clean()
 * 3) Возможны ситуации, когда первый параметр — объект типа @see \Magento\Framework\DataObject
 * В таком случае мы трактуем этот объект как массив, а не как носитель кэша.
 * 2017-07-13
 * Эта функция может получать 2 или 3 параметра.
 * Третий параметр — это результат функции по умолчанию, этот параметр опционален.
 * Первые два параметра — это:
 * a) некий контейнер (массив или объект)
 * b) accessor: некий ключ доступа к полю контейнера (строка или Closure).
 * Эти параметры могут быть переданы в произвольном порядке относительно друг друга
 * (но третий параметр всегда должен быть в конце.)
 * @used-by df_ci_get()
 * @used-by df_credentials()
 * @used-by df_fe_fc()
 * @used-by df_package()
 * @used-by df_trd()
 * @used-by \Df\Config\A::get()
 * @used-by \Df\Config\Backend::fc()
 * @used-by \Df\Framework\Form\Element\Fieldset::v()
 * @used-by \Df\Framework\Request::clean()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\Method::ii()
 * @used-by \Df\Payment\TM::req()
 * @used-by \Df\Payment\TM::res0()
 * @used-by \Df\Payment\W\Reader::r()
 * @used-by \Df\Payment\W\Reader::test()
 * @used-by \Df\PaypalClone\Signer::v()
 * @param mixed[] ...$args
 * @return DataObject|array(string => mixed)|mixed|null
 */
function dfak(...$args) {
	/** @var object $o */
	if ($args[1] instanceof \Closure) {
		$o = array_shift($args);
		df_assert(is_object($o));
	}
	/** @var \Closure|DataObject|array(string => mixed $a */
	$a = array_shift($args);
	$a = !$a instanceof \Closure ? $a : (isset($o) ? dfc($o, $a, [], false, 1) : dfcf($a, [], [], false, 1));
	/** @var string|string[]|null $k */
	$k = dfa($args, 0);
	/** @var DataObject|array(string => mixed)|mixed|null $result */
	if (is_null($k)) {
		$result = $a;
	}
	else {
		if ($a instanceof DataObject) {
			$a = $a->getData();
		}
		$result = is_array($k) ? dfa($a, $k) : dfa_deep($a, $k, dfa($args, 1));
	}
	return $result;
}

/**
 * 2016-08-21
 * @param mixed[] $a
 * @return A
 */
function dfao(array $a) {return new A($a);}

/**
 * 2015-02-07
 * Аналог @see array_change_key_case() с поддержкой UTF-8.
 * Реализацию взял отсюда: http://php.net/manual/function.array-change-key-case.php#107715
 * Обратите внимание, что @see array_change_key_case() некорректно работает с UTF-8.
 * Например:
		$countries = array('Россия' => 'RU', 'Украина' => 'UA', 'Казахстан' => 'KZ');
	array_change_key_case($countries, CASE_UPPER)
 * вернёт:
	(
		[РнссШя] => RU
		[УЪраШна] => UA
		[Њазахстан] => KZ
	)
 *
 * 2017-02-01
 * Отныне стал использовать константы MB_CASE_LOWER и MB_CASE_UPPER вместо CASE_LOWER и CASE_UPPER.
 * Обратите внимание, что они имеют противоположные значения:
 * CASE_LOWER = 0, а MB_CASE_LOWER = 1
 * CASE_UPPER = 1, а MB_CASE_UPPER = 0.
 *
 * @used-by dfa_key_lc()
 * @used-by dfa_key_uc()
 * @param array(string => mixed) $a
 * @param int $c
 * @return array(string => mixed)
 */
function dfa_key_case(array $a, $c) {return dfa_key_transform($a, function($k) use($c) {return
	mb_convert_case($k, $c, 'UTF-8')
;});}

/**
 * 2017-09-03
 * @used-by \Dfe\Qiwi\API\Validator::codes()
 * @uses df_int()
 * @see df_int_simple()
 * @param array(int|string => mixed) $a
 * @return array(int => mixed)
 */
function dfa_key_int(array $a) {return dfa_key_transform($a, 'df_int');}

/**
 * 2017-02-01
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function dfa_key_lc(array $a) {return dfa_key_case($a, MB_CASE_LOWER);}

/**
 * 2017-02-01 Функция принимает аргументы в любом порядке.
 * @see df_map_kr()
 * @used-by df_headers()
 * @used-by dfa_key_case()
 * @used-by dfa_key_int()
 * @used-by \Df\Framework\Request::extra()
 * @used-by \Df\Sentry\Client::tags_context()
 * @used-by \Df\Sentry\Extra::adjust()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function dfa_key_transform($a1, $a2) {
	/** @var callable $f */
	/** @var array(int|string => mixed)|\Traversable $a */
	list($a, $f) = dfaf($a1, $a2);
	df_assert_callable($f);
	$a = df_ita(df_assert_traversable($a));
	return array_combine(array_map($f, array_keys($a)), array_values($a));
}

/**
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @param array(string => mixed) $a
 * @return array(string => mixed)
 */
function dfa_key_uc(array $a) {return dfa_key_case($a, MB_CASE_UPPER);}

/**
 * 2016-09-07
 * 2017-03-06
 * @uses mb_substr() корректно работает с $length = null
 * @used-by \Df\Payment\Charge::metadata()
 * @param string[] $a
 * @param int|null $length
 * @return string[]
 */
function dfa_chop(array $a, $length) {return df_map('mb_substr', $a, [0, $length]);}

/**               
 * 2016-11-25
 * @param string[]|int[] $a
 * @return array(int|string => int|string)
 */
function dfa_combine_self(array $a) {return array_combine($a, $a);}

/**
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации ключ1/ключ2/ключ3
 * Например: dfa_deep(array('test' => array('eee' => 3)), 'test/eee') вернёт «3».
 * Обратите внимание, что ядро Magento реализует аналогичный алгоритм
 * в методе @see \Magento\Framework\DataObject::getData()
 * Наша функция работает не только с объектами @see \Magento\Framework\DataObject, но и с любыми массивами.
 * 2017-03-28
 * Сегодня заметил, что успешно работают пути типа 'transactions/0'
 * в том случае, когда ключ верхнего уровня возвращает массив с целочисленными индексами.
 * @used-by \Df\API\Document::offsetExists()
 * @used-by \Df\API\Document::offsetGet()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @param array(string => mixed) $a
 * @param string|string[] $path
 * @param mixed $d [optional]
 * @return mixed|null
 */
function dfa_deep(array $a, $path, $d = null) {
	/** @var mixed|null $result */
	if (is_array($path)) {
		$pathParts = $path;
	}
	else {
		df_param_sne($path, 1);
		if (isset($a[$path])) {
			$result = $a[$path];
		}
		else {
			/**
			 * 2015-02-06
			 * Обратите внимание, что если разделитель отсутствует в строке,
			 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
			 * Это вполне укладывается в наш универсальный алгоритм.
			 */
			/** @var string[] $pathParts */
			$pathParts = df_explode_xpath($path);
		}
	}
	if (!isset($result)) {
		$result = null;
		/** @noinspection PhpUndefinedVariableInspection */
		while ($pathParts) {
			$result = dfa($a, array_shift($pathParts));
			if (is_array($result)) {
				$a = $result;
			}
			else {
				if ($pathParts) {
					// Ещё не прошли весь путь, а уже наткнулись на не-массив.
					$result = null;
				}
				break;
			}
		}
	}
	return is_null($result) ? $d : $result;
}

/**
 * 2015-12-07
 * @used-by \Df\API\Document::offsetSet()
 * @param array(string => mixed) $array
 * @param string|string[] $path
 * @param mixed $value
 */
function dfa_deep_set(array &$array, $path, $value) {
	if (is_array($path)) {
		$pathParts = $path;
	}
	else {
		df_param_sne($path, 1);
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		/** @var string[] $pathParts */
		$pathParts = df_explode_xpath($path);
	}
	/** @var array(string => mixed) $a */
	$a = &$array;
	while ($pathParts) {
		/** @var string $key */
		$key = array_shift($pathParts);
		if (!isset($a[$key])) {
			$a[$key] = [];
		}
		$a = &$a[$key];
		if (!is_array($a)) {
			$a = [];
		}
	}
	$a = $value;
}

/**
 * 2017-07-13
 * @see dfa_unset()
 * @used-by \Df\API\Document::offsetUnset()
 * @param array(string => mixed) $a
 * @param string|string[] $path
 */
function dfa_deep_unset(array &$a, $path) {
	if (!is_array($path)) {
		df_param_sne($path, 1);
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		$path = df_explode_xpath($path);
	}
	/**
	 * 2017-07-13
	 * @uses array_shift не выдаёт предупреждений для пустого массива.
	 * @var string|null $first
	 */
	if ($first = array_shift($path)) {
		if (!$path) {
			unset($a[$first]);
		}
		else {
			dfa_deep_unset($a[$first], $path);
		}
	}
}

/**
 * Эта функция отличается от @uses array_fill() только тем,
 * что разрешает параметру $length быть равным нулю.
 * Если $length = 0, то функция возвращает пустой массив.
 * @uses array_fill() разрешает параметру $num (аналог $length)
 * быть равным нулю только начиная с PHP 5.6:
 * http://php.net/manual/function.array-fill.php
 * «5.6.0	num may now be zero. Previously, num was required to be greater than zero»
 * @param int $startIndex
 * @param int $length
 * @param mixed $value
 * @return mixed[]
 */
function dfa_fill($startIndex, $length, $value) {
	return !$length ? [] : array_fill($startIndex, $length, $value);
}

/**
 * 2016-03-25
 * http://stackoverflow.com/a/1320156
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @param array $a
 * @return mixed[]
 */
function dfa_flatten(array $a) {
	/** @var mixed[] $result */
	$result = [];
	array_walk_recursive($a, function($a) use(&$result) {$result[]= $a;});
	return $result;
}

/**
 * 2016-07-31
 * @param \Traversable|array(int|string => DataObject) $collection
 * @return int[]|string[]
 */
function dfa_ids($collection) {return df_each($collection, 'getId');}

/**
 * 2016-08-26
 * Вставляет новые элементы внутрь массива.
 * http://php.net/manual/function.array-splice.php
 * Если нужно вставить только один элемент, то необязательно обрамлять его в массив.
 *
 * 2016-11-23
 * Достоинство этой функции перед @uses array_splice()
 * ещё и в отсутствии требования передачи первого параметра по ссылке.
 *
 * 2016-11-24
 * Отныне функция правильно работает с ассоциативными массивами.
 *
 * @param mixed[] $a
 * @param int $pos
 * @param mixed|mixed[] $add
 * @return mixed[]
 */
function dfa_insert(array $a, $pos, $add) {
	if (!is_array($add) || !df_is_assoc($add)) {
		array_splice($a, $pos, 0, $add);
	}
	else {
		/**
		 * 2016-11-24
		 * Отныне функция правильно работает с ассоциативными массивами.
		 * http://stackoverflow.com/a/1783125
		 */
		$a = array_slice($a, 0, $pos, true) + $add + array_slice($a, $pos, null, true);
	}
	return $a;
}

/**
 * 2017-10-28
 * Plain `array_merge($r, $b)` works wronly,
 * if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by dfa_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function dfa_merge_numeric(array $r, array $b) {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'RU' => 'Россия', 'KZ' => 'Казахстан', 'TJ' => 'Таджикистан','US' => 'США','CA' => 'Канада'
 		);
		$priorityKeys = array('TJ', 'CA');
		print_r(dfa_prepend_by_keys($source, $priorityKeys));
 * Вернёт:
	 Array
	 (
		 [TJ] => Таджикистан
		 [CA] => Канада
		 [RU] => Россия
		 [KZ] => Казахстан
		 [US] => США
	 )
 * http://3v4l.org/QYffO
 * Обратите внимание, что @uses array_flip() корректно работает с пустыми массивами:
	print_r(array_flip([]));
 * вернёт array
 * http://3v4l.org/Kd01X
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityKeys
 * @return array(string => mixed)
 */
function dfa_prepend_by_keys(array $source, array $priorityKeys) {return
	dfa_select_ordered($source, $priorityKeys) + $source
;}

/**
 * 2015-02-07
 * Функция предназначена для работы только с ассоциативными массивами!
 * Фантастически лаконичное и красивое решение!
 * Вынес его в отдельную функцию, чтобы не забыть!
 * Например:
		$source = array(
			'Россия' => 'RU'
			,'Казахстан' => 'KZ'
			,'Таджикистан' => 'TJ'
			,'США' => 'US'
			,'Канада' => 'CA'
		);
		$priorityValues = array('TJ', 'CA');
		print_r(dfa_prepend_by_values($source, $priorityValues));
 * вернёт:
		Array
		(
			[Таджикистан] => TJ
			[Канада] => CA
			[Россия] => RU
			[Казахстан] => KZ
			[США] => US
		)
 * http://3v4l.org/tNms4
 * @uses dfa_prepend_by_keys()
 * @used-by Df_Directory_Model_Resource_Country_Collection::toOptionArrayRm()
 * @param array(string => mixed) $source
 * @param string[] $priorityValues
 * @return array(string => mixed)
 */
function dfa_prepend_by_values(array $source, array $priorityValues) {return array_flip(
	dfa_prepend_by_keys(array_flip($source), $priorityValues)
);}

/**
 * 2016-07-31
 * Возвращает повторяющиеся элементы исходного массива (не повторяя их).
 * https://3v4l.org/YEf5r
 * В алгоритме пользуемся тем, что @uses array_unique() сохраняет ключи исходного массива.
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @param array $a
 * @return array
 */
function dfa_repeated(array $a) {return array_values(array_unique(array_diff_key($a, array_unique($a))));}

/**
 * 2015-02-11
 * Из ассоциативного массива $source выбирает элементы с ключами $keys.
 * В отличие от @see dfa_select_ordered() не учитывает порядок ключей $keys
 * и поэтому работает быстрее, чем @see dfa_select_ordered().
 * @param array(string => string)|\Traversable $source
 * @param string[] $keys
 * @return array(string => string)
 */
function dfa_select($source, array $keys)  {return
	array_intersect_key(df_ita($source), array_fill_keys($keys, null))
;}

/**
 * 2015-02-08
 * Из ассоциативного массива $source выбирает элементы с ключами $orderedKeys
 * и возвращает их в том же порядке, в каком они перечислены в $orderedKeys.
 * Если порядок ключей не важен, но используйте более быстрый аналог @see dfa_select().
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @used-by \Dfe\Dragonpay\Signer\Request::values()
 * @used-by \Dfe\IPay88\Signer\Request::values()
 * @used-by \Dfe\IPay88\Signer\Response::values()
 * @used-by \Dfe\Robokassa\Signer\Request::values()
 * @used-by \Dfe\Robokassa\Signer\Response::values()
 * @param array(string => string)|\Traversable $source
 * @param string[] $orderedKeys
 * @return array(string => string)
 */
function dfa_select_ordered($source, array $orderedKeys)  {
	$resultKeys = array_fill_keys($orderedKeys, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * Previously, I had the following code here during 2.5 years:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It works wronly, if $source contains SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 * 
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * http://php.net/manual/en/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * http://php.net/manual/en/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = dfa_merge_numeric($resultKeys, df_ita($source));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}

/**
 * Работает в разы быстрее, чем @see array_unique()
 * «Just found that array_keys(array_flip($array)); is amazingly faster than array_unique();.
  * About 80% faster on 100 element array,
  * 95% faster on 1000 element array
  * and 99% faster on 10000+ element array.»
 * http://stackoverflow.com/questions/5036504/php-performance-question-faster-to-leave-duplicates-in-array-that-will-be-searc#comment19991540_5036538
 * http://www.php.net/manual/en/function.array-unique.php#70786
 * 2015-02-06
 * Обратите внимание, что т.к. алгоритм @see dfa_unique_fast() использует @uses array_flip(),
 * то @see dfa_unique_fast() можно применять только в тех ситуациях,
 * когда массив содержит только строки и целые числа,
 * иначе вызов @uses array_flip() завершится сбоем уровня E_WARNING:
 * «array_flip(): Can only flip STRING and INTEGER values»
 * http://magento-forum.ru/topic/4695/
 * В реальной практике сбой случается, например, когда массив содержит значение null:
 * http://3v4l.org/bat52
 * Пример кода, приводящего к сбою: dfa_unique_fast(array(1, 2, 2, 3, null))
 * В то же время, несмотря на E_WARNING, метод всё-таки возвращает результат,
 * правда, без недопустимых значений:
 * при подавлении E_WARNING dfa_unique_fast(array(1, 2, 2, 3, null)) вернёт:
 * array(1, 2, 3).
 * Более того, даже если сбойный элемент содержится в середине исходного массива,
 * то результат при подавлении сбоя E_WARNING будет корректным (без недопустимых элементов):
 * dfa_unique_fast(array(1, 2, null,  2, 3)) вернёт тот же результат array(1, 2, 3).
 * http://3v4l.org/uvJoI
 * По этой причине добавил оператор @ перед @uses array_flip()
 * @param array(int|string => int|string) $a
 * @return array(int|string => int|string)
 */
function dfa_unique_fast(array $a) {return
	/** @noinspection PhpUsageOfSilenceOperatorInspection */ array_keys(@array_flip($a))
;}

/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() корректно работает с пустыми массивами.
 * @used-by \Dfe\Dynamics365\Button::onFormInitialized()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @param array(string => mixed) $a
 * @param string[] ...$keys
 * @return array(string => mixed)
 */
function dfa_unset(array $a, ...$keys) {return array_diff_key($a, array_flip(df_args($keys)));}

/**
 * Алгоритм взят отсюда:
 * http://php.net/manual/function.array-unshift.php#106570
 * @param array(string => mixed) $a
 * @param string $k
 * @param mixed $v
 */
function dfa_unshift_assoc(&$a, $k, $v)  {
	$a = array_reverse($a, $preserve_keys = true);
	$a[$k] = $v;
	$a = array_reverse($a, $preserve_keys = true);
}

/**
 * 2016-09-05
 * @used-by df_cfg_save()
 * @used-by df_url_bp()
 * @used-by \Df\Directory\FE\Currency::v()
 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
 * @used-by \Df\GingerPaymentsBase\Choice::title()
 * @used-by \Df\GingerPaymentsBase\Method::optionE()
 * @used-by \Df\GingerPaymentsBase\Method::optionI()
 * @used-by \Df\PaypalClone\W\Event::logTitleSuffix()
 * @used-by \Dfe\AllPay\W\Reader::te2i()
 * @used-by \Dfe\IPay88\W\Event::optionTitle()
 * @used-by \Dfe\Moip\Facade\Card::brand()
 * @used-by \Dfe\Moip\Facade\Card::logoId()
 * @used-by \Dfe\Moip\Facade\Card::numberLength()
 * @used-by \Dfe\Paymill\Facade\Card::brand()
 * @used-by \Dfe\PostFinance\W\Event::optionTitle()
 * @used-by \Dfe\Robokassa\W\Event::optionTitle()
 * @used-by \Dfe\Square\Facade\Card::brand()
 * @used-by \Dfe\Stripe\FE\Currency::getComment()
 * @used-by \Dfe\Stripe\RedirectUrl::p()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function dftr($v, array $map) {return dfa($map, $v, $v);}
<?php
use Df\Qa\Dumper;

/**
 * Обратите внимание, что мы намеренно не используем для @uses Df_Core_Dumper
 * объект-одиночку, потому что нам надо вести учёт выгруженных объектов,
 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
 * @see df_type()
 * @see df_print_params()
 * @used-by df_assert_eq()
 * @used-by df_bool()
 * @used-by df_extend()
 * @used-by df_sentry()
 * @used-by df_type()
 * @used-by dfc()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Form\Element\Text::getValue()
 * @used-by \Df\Geo\T\Basic::t01()
 * @used-by \Dfe\Dynamics365\Test\OAuth::discovery()
 * @used-by \Dfe\Portal\Test\Basic::t01()
 * @used-by \Dfe\Portal\Test\Basic::t02()
 * @used-by \Dfe\Robokassa\Test\Basic::t01()
 * @param \Magento\Framework\DataObject|mixed[]|mixed $v
 * @return string
 */
function df_dump($v) {return Dumper::i()->dump($v);}

/**
 * Эта функция имеет 2 отличия от @see print_r():
 * 1) она корректно обрабатывает объекты и циклические ссылки
 * 2) она для верхнего уровня не печатает обрамляющее «Array()» и табуляцию, т.е. вместо
 *		Array
 *		(
 *			[pattern_id] => p2p
 *			[to] => 41001260130727
 *			[identifier_type] => account
 *			[amount] => 0.01
 *			[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *			[message] =>
 *			[label] => localhost.com
 *		)
 * выводит:
 *	[pattern_id] => p2p
 *	[to] => 41001260130727
 *	[identifier_type] => account
 *	[amount] => 0.01
 *	[comment] => Оплата заказа №100000099 в магазине localhost.com.
 *	[message] =>
 *	[label] => localhost.com
 *
 * @see df_dump()
 * @see df_type()
 * @used-by \Df\Core\Validator::check()
 * @used-by \Df\Core\Validator::resolveForProperty()
 * @param array(string => string) $params
 * @return mixed
 */
function df_print_params(array $p) {return Dumper::i()->dumpArrayElements($p);}

/**
 * 2015-04-05
 * @see df_dump()
 * @see df_print_params()
 * @used-by df_ar()        
 * @used-by df_assert_gd()
 * @used-by df_assert_traversable()
 * @used-by df_customer()
 * @used-by df_oq_currency_c()
 * @used-by df_order()
 * @used-by df_result_s()
 * @used-by dfaf()
 * @used-by dfpex_args()
 * @used-by \Df\Core\Validator::check()
 * @used-by \Df\Core\Exception\InvalidObjectProperty::__construct()
 * @param mixed $v
 * @return string
 */
function df_type($v) {return is_object($v) ? sprintf('an object: %s', get_class($v), df_dump($v)) : (is_array($v)
	? (10 < ($c = count($v)) ? "«an array of $c elements»" : 'an array: ' . df_dump($v))
	/** 2020-02-04 We should not use @see df_desc() here */
	: (is_null($v) ? '`null`' : sprintf('«%s» (%s)', df_string($v), gettype($v)))
);}
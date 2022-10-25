<?php
namespace Df\Qa;
final class Dumper {
	/**
	 * @used-by df_dump()
	 * @used-by self::dumpArrayElements()
	 * @param mixed $v
	 * @return string
	 */
	function dump($v) {return is_object($v) ? $this->dumpObject($v) : (
		is_array($v) ? $this->dumpArray($v) : (is_bool($v) ? df_bts($v) : (is_string($v) ? $v : print_r($v, true)))
	);}

	/**
	 * @used-by self::dump()
	 * @param mixed $a
	 * @return string
	 */
	private function dumpArray(array $a) {return "[\n" . df_tab_multiline($this->dumpArrayElements($a)) . "\n]";}

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
	 * 2015-01-25 @uses df_ksort() для удобства сравнения двух версий массива/объекта в Araxis Merge.
	 * @see df_kv()
	 * @used-by self::dumpArray()
	 * @used-by self::dumpObject()
	 * @param mixed[]|array(string => mixed) $a
	 * @return string
	 */
	private function dumpArrayElements(array $a) {return df_cc_n(df_map_k(df_ksort($a), function($k, $v) {return
		"$k: {$this->dump($v)}"
	;}));}

	/**
	 * @used-by self::dump()
	 * @param object $o
	 * @return string
	 */
	private function dumpObject($o) {/** @var string $r */
		$hash = spl_object_hash($o); /** @var string $hash */
		if (isset($this->_dumped[$hash])) {
			$r = sprintf('[recursion: %s]', get_class($o));
		}
		else {
			$this->_dumped[$hash] = true;
			$r = !df_has_gd($o)
				? sprintf("%s %s", get_class($o), df_json_encode_partial($o))
				: sprintf("%s(%s\n)", get_class($o), df_tab_multiline($this->dumpArrayElements($o->getData())))
			;
		}
		return $r;
	}

	/**
	 * @used-by self::dumpObject()
	 * @var array(string => bool)
	 */
	private $_dumped = [];

	/**
	 * Обратите внимание, что мы намеренно не используем для этого класса объект-одиночку,
	 * потому что нам надо вести учёт выгруженных объектов,
	 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
	 */
	static function i():self {return new self;}
}
<?php
namespace Df\Qa;
/** 2023-07-25 @todo Use YAML instead of JSON for `df_dump()` https://github.com/mage2pro/core/issues/254 */
final class Dumper {
	/**
	 * @used-by df_dump()
	 * @used-by self::dumpArrayElements()
	 * @param mixed $v
	 */
	function dump($v):string {return is_object($v) ? $this->dumpObject($v) : (
		is_array($v) ? $this->dumpArray($v) : (is_bool($v) ? df_bts($v) : (is_string($v) ? $v : print_r($v, true)))
	);}

	/** @used-by self::dump() */
	private function dumpArray(array $a):string {return
        # 2023-07-25
        # "Return JSON from `\Df\Qa\Dumper::dumpArray()` for arrays without object elements":
        # https://github.com/mage2pro/core/issues/252
        !dfa_has_objects($a) ? df_json_encode($a) : "[\n" . df_tab_multiline($this->dumpArrayElements($a)) . "\n]"
    ;}

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
	 */
	private function dumpArrayElements(array $a):string {return df_cc_n(df_map_k(df_ksort($a), function($k, $v) {return
		"$k: {$this->dump($v)}"
	;}));}

	/**
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
	 * @used-by self::dump()
	 * @param object $o
	 */
	private function dumpObject($o):string {/** @var string $r */
		$hash = spl_object_hash($o); /** @var string $hash */
		if (isset($this->_dumped[$hash])) {
			$r = sprintf('[recursion: %s]', get_class($o));
		}
		else {
			$this->_dumped[$hash] = true;
			# 2023-07-26 "`df_dump()` should handle `Traversable` similar to arrays": https://github.com/mage2pro/core/issues/253
			/** @var array(string => mixed)|null $a */
			$a = df_has_gd($o) ? df_gd($o) : (is_iterable($o) ? df_ita($o) : null);
			$r = is_null($a)
				? sprintf("%s %s", get_class($o), df_json_encode_partial($o))
				: sprintf("%s(\n%s\n)", get_class($o), df_tab_multiline($this->dumpArrayElements($a)))
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
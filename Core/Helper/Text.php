<?php
namespace Df\Core\Helper;
final class Text {
	/** @used-by \Df\Core\Text\Regex::isSubjectMultiline() */
	function isMultiline(string $s):bool {return df_contains($s, "\n") || df_contains($s, "\r");}

	/**
	 * @used-by df_quote_double()
	 * @used-by df_quote_russian()
	 * @used-by df_quote_single()
	 * @param string|string[]|array(string => string) $s
	 * @return string|string[]
	 */
	function quote($s, string $t = self::QUOTE__RUSSIAN) {
		if ('"' === $t) {
			$t = self::QUOTE__DOUBLE;
		}
		elseif ("'" === $t) {
			$t = self::QUOTE__SINGLE;
		}
		static $m = [
			self::QUOTE__DOUBLE => ['"', '"'], self::QUOTE__RUSSIAN => ['«', '»'], self::QUOTE__SINGLE => ["'", "'"]
		]; /** @var array(string => string[]) $m */
		$quotes = dfa($m, $t); /** @var string[] $quotes */
		if (!is_array($quotes)) {
			df_error("An unknown quote: «{$t}».");
		}
		/**
		 * 2016-11-13 It injects the value $s inside quotes.
		 * @param string $s
		 * @return string
		 */
		$f = function($s) use($quotes) {return implode($s, $quotes);};
		return !is_array($s) ? $f($s) : array_map($f, $s);
	}

	/**
	 * 2015-03-03 Алгоритм аналогичен @see singleLine()
	 * 2015-07-07
	 * 1) Раньше алгоритм был таким: `strtr($text, "\r\n", '  ')`.
	 * Однако он не совсем правилен, потому что если перенос строки записан в формате Windows
	 * (то есть, в качестве переноса строки используется последовательность \r\n),
	 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один:
	 * «If given three arguments,
	 * this function returns a copy of str where all occurrences of each (single-byte) character in from
	 * have been translated to the corresponding character in to,
	 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
	 * where $n is a valid offset in both arguments.
	 * If from and to have different lengths,
	 * the extra characters in the longer of the two are ignored.
	 * The length of str will be the same as the return value's.»
	 * https://php.net/strtr
	 * Новый алгоритм взял отсюда:  http://stackoverflow.com/a/20717751
	 * 2021-12-13 @deprecated It is unused.
	 * @param string $s
	 */
	function removeLineBreaks($s):string {return str_replace(["\r\n", "\r", "\n"], ' ', $s);}

	/**
	 * 2015-03-03 Алгоритм аналогичен @see removeLineBreaks()
	 * 2015-07-07
	 * Раньше алгоритм был таким: `strtr($text, "\r\n", '  ')`.
	 * Однако он не совсем правилен, потому что если перенос строки записан в формате Windows
	 * (то есть, в качестве переноса строки используется последовательность \r\n),
	 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один:
	 * «If given three arguments,
	 * this function returns a copy of str where all occurrences of each (single-byte) character in from
	 * have been translated to the corresponding character in to,
	 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
	 * where $n is a valid offset in both arguments.
	 * If from and to have different lengths,
	 * the extra characters in the longer of the two are ignored.
	 * The length of str will be the same as the return value's.»
	 * https://php.net/strtr
	 * Новый алгоритм взял отсюда: http://stackoverflow.com/a/20717751
	 * @used-by df_extend()
	 * @param string $s
	 * @return string
	 */
	function singleLine($s) {return str_replace(["\r\n", "\r", "\n", "\t"], ' ', $s);}

	/**
	 * @used-by df_quote_double()
	 * @used-by self::quote()
	 */
	const QUOTE__DOUBLE = 'double';

	/**
	 * @used-by df_quote_russian()
	 * @used-by self::quote()
	 */
	const QUOTE__RUSSIAN = 'russian';

	/**
	 * @used-by df_quote_single()
	 * @used-by self::quote()
	 */
	const QUOTE__SINGLE = 'single';

	/** @used-by df_t() */
	static function s():self {static $r; return $r ? $r : $r = new self;}
}
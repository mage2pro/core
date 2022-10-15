<?php
namespace Df\Core\Helper;
class Text {
	/**
	 * @used-by \Df\Core\Text\Regex::isSubjectMultiline()
	 * @param string $s
	 * @return bool
	 */
	function isMultiline($s) {return df_contains($s, "\n") || df_contains($s, "\r");}

	/**
	 * Простой, неполный, но практически адекватный для моих ситуаций
	 * способ опредилелить, является ли строка регулярным выражением.
	 * @param string $s
	 * @return string
	 */
	function isRegex($s) {return df_starts_with($s, '#');}

	/**
	 * @param string $s
	 * @return string
	 */
	function normalizeName($s) {return mb_strtoupper(df_trim($s));}

	/**
	 * @param string $s
	 * @return string[]
	 */
	function parseTextarea($s) {return df_clean(df_trim(df_explode_n(df_trim($s))));}

	/**
	 * @used-by df_quote_double()
	 * @used-by df_quote_russian()
	 * @used-by df_quote_single()
	 * @param string|string[]|array(string => string) $s
	 * @param string $t [optional]
	 * @return string|string[]
	 */
	function quote($s, $t = self::QUOTE__RUSSIAN) {
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
	 * http://php.net/strtr
	 * Новый алгоритм взял отсюда:  http://stackoverflow.com/a/20717751
	 * 2021-12-13 @deprecated It is unused.
	 * @param string $s
	 * @return string
	 */
	function removeLineBreaks($s) {return str_replace(["\r\n", "\r", "\n"], ' ', $s);}

	/**
	 * http://www.php.net/str_ireplace
	 * @param string $search
	 * @param string $replace
	 * @param string $subject
	 * @param int|null $count [optional]
	 * @return string
	 */
	function replaceCI($search, $replace, $subject, $count = null) {
		if (!is_array($search)) {
			$slen = mb_strlen($search);
			if (0 === $slen) {
				return $subject;
			}
			$lendif = mb_strlen($replace) - mb_strlen($search);
			$search = mb_strtolower($search);
			$search = preg_quote($search);
			$lstr = mb_strtolower($subject);
			$i = 0;
			$matched = 0;
			/** @var string[] $matches */
			$matches = [];
			while (1 === preg_match('/(.*)'.$search.'/Us',$lstr, $matches)) {
				if ($i === $count ) {
					break;
				}
				$mlen = mb_strlen($matches[0]);
				$lstr = mb_substr($lstr, $mlen);
				$subject =
					substr_replace(
						$subject, $replace, $matched+strlen($matches[1]), $slen
					)
				;
				$matched += $mlen + $lendif;
				$i++;
			}
			return $subject;
		}
		else {
			foreach (array_keys($search) as $k ) {
				if (is_array($replace)) {
					if (array_key_exists($k, $replace)) {
						$subject = $this->replaceCI($search[$k], $replace[$k], $subject, $count);
					}
					else {
						$subject = $this->replaceCI($search[$k], '', $subject, $count);
					}
				} else {
					$subject = $this->replaceCI($search[$k], $replace, $subject, $count);
				}
			}
			return $subject;
		}
	}

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
	 * http://php.net/strtr
	 * Новый алгоритм взял отсюда: http://stackoverflow.com/a/20717751
	 * @used-by df_extend()
	 * @param string $s
	 * @return string
	 */
	function singleLine($s) {return str_replace(["\r\n", "\r", "\n", "\t"], ' ', $s);}

	/**
	 * Источник алгоритма: http://stackoverflow.com/a/14338869
	 * @param string $s1
	 * @param string $s2
	 * @return string
	 */
	function xor_($s1, $s2) {return bin2hex(pack('H*', $s1) ^ pack('H*', $s2));}

	/**
	 * @used-by quote()
	 * @used-by df_quote_double()
	 */
	const QUOTE__DOUBLE = 'double';

	/**
	 * @used-by quote()
	 * @used-by df_quote_russian()
	 */
	const QUOTE__RUSSIAN = 'russian';

	/**
	 * @used-by quote()
	 * @used-by df_quote_single()
	 */
	const QUOTE__SINGLE = 'single';

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}
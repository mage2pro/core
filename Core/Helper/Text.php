<?php
namespace Df\Core\Helper;
use Df\Core\Format\NounForAmounts;
class Text {
	/**
	 * @used-by df_day_noun()
	 * @param int $a
	 * @param array $forms
	 * @return string
	 */
	function getNounForm($a, array $forms) {return NounForAmounts::s()->getForm(
		df_param_integer($a, 0), $forms
	);}

	/**
	 * @used-by \Df\Core\Text\Regex::isSubjectMultiline()
	 * @param string $text
	 * @return bool
	 */
	function isMultiline($text) {return df_contains($text, "\n") || df_contains($text, "\r");}

	/**
	 * Простой, неполный, но практически адекватный для моих ситуаций
	 * способ опредилелить, является ли строка регулярным выражением.
	 * @param string $text
	 * @return string
	 */
	function isRegex($text) {return df_starts_with($text, '#');}

	/**
	 * @param string $name
	 * @return string
	 */
	function normalizeName($name) {return mb_strtoupper(df_trim($name));}

	/**
	 * @param string $text
	 * @return string[]
	 */
	function parseTextarea($text) {return df_clean(df_trim(df_explode_n(df_trim($text))));}

	/**
	 * @param string|string[]|array(string => string) $text
	 * @param string $type [optional]
	 * @return string|string[]
	 */
	function quote($text, $type = self::QUOTE__RUSSIAN) {
		if ('"' === $type) {
			$type = self::QUOTE__DOUBLE;
		}
		elseif ("'" === $type) {
			$type = self::QUOTE__SINGLE;
		}
		/** @var array $quotesMap */
		static $quotesMap = [
			self::QUOTE__DOUBLE => ['"', '"']
			,self::QUOTE__RUSSIAN => ['«', '»']
			,self::QUOTE__SINGLE => ['\'', '\'']
		];
		/** @var string[] $quotes */
		$quotes = dfa($quotesMap, $type);
		if (!is_array($quotes)) {
			df_error("Неизвестный тип кавычки: «{$type}».");
		}
		/**
		 * 2016-11-13
		 * Обратите внимание на красоту решения: мы «склеиваем кавычки»,
		 * используя в качестве промежуточного звена исходную строку.
		 * @param string $text
		 * @return string
		 */
		$f = function($text) use($quotes) {return implode($text, $quotes);};
		return !is_array($text) ? $f($text) : array_map($f, $text);
	}

	/**
	 * Удаляет с начала каждой строки текста заданное количество пробелов
	 * @param string $text
	 * @param int $numSpaces
	 * @return string
	 */
	function removeLeadingSpacesMultiline($text, $numSpaces) {return
		implode(explode(str_repeat(' ', $numSpaces), $text))
	;}

	/**
	 * 2015-03-03
	 * Алгоритм аналогичен @see singleLine()
	 *
	 * 2015-07-07
	 * Раньше алгоритм был таким:
	 	return strtr($text, "\r\n", '  ');
	 * Однако он не совсем правилен,
	 * потому что если перенос строки записан в формате Windows
	 * (то есть, в качестве переноса строки используется последовательность \r\n),
	 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один.
	 *
	 * «If given three arguments,
	 * this function returns a copy of str where all occurrences of each (single-byte) character in from
	 * have been translated to the corresponding character in to,
	 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
	 * where $n is a valid offset in both arguments.
	 * If from and to have different lengths,
	 * the extra characters in the longer of the two are ignored.
	 * The length of str will be the same as the return value's.»
	 * http://php.net/strtr
	 *
	 * Новый алгоритм взял отсюда:
	 * http://stackoverflow.com/a/20717751
	 *
	 * @param string $text
	 * @return string
	 */
	function removeLineBreaks($text) {
		/** @var string[] $symbolsToRemove */
		static $symbolsToRemove = ["\r\n", "\r", "\n"];
		return str_replace($symbolsToRemove, ' ', $text);
	}

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
					if (array_key_exists($k,$replace)) {
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
	 * 2015-03-03
	 * Алгоритм аналогичен @see removeLineBreaks()
	 *
	 * 2015-07-07
	 * Раньше алгоритм был таким:
	 	return strtr($text, "\r\n", '  ');
	 * Однако он не совсем правилен,
	 * потому что если перенос строки записан в формате Windows
	 * (то есть, в качестве переноса строки используется последовательность \r\n),
	 * то прошлый алгоритм заменит эту последовательность на 2 пробела, а надо — на один.
	 *
	 * «If given three arguments,
	 * this function returns a copy of str where all occurrences of each (single-byte) character in from
	 * have been translated to the corresponding character in to,
	 * i.e., every occurrence of $from[$n] has been replaced with $to[$n],
	 * where $n is a valid offset in both arguments.
	 * If from and to have different lengths,
	 * the extra characters in the longer of the two are ignored.
	 * The length of str will be the same as the return value's.»
	 * http://php.net/strtr
	 *
	 * Новый алгоритм взял отсюда:
	 * http://stackoverflow.com/a/20717751
	 *
	 * @param string $text
	 * @return string
	 */
	function singleLine($text) {
		/** @var string[] $symbolsToRemove */
		static $symbolsToRemove = ["\r\n", "\r", "\n", "\t"];
		return str_replace($symbolsToRemove, ' ', $text);
	}

	/**
	 * Источник алгоритма:
	 * http://stackoverflow.com/a/14338869
	 * @param string $string1
	 * @param string $string2
	 * @return string
	 */
	function xor_($string1, $string2) {return bin2hex(pack('H*', $string1) ^ pack('H*', $string2));}

	const QUOTE__DOUBLE = 'double';
	const QUOTE__RUSSIAN = 'russian';
	const QUOTE__SINGLE = 'single';

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @param string[] $matches
	 * @return string
	 */
	private static function nl2brCallback(array $matches) {
		return str_replace('{rm-newline}', '{rm-newline-preserve}', dfa($matches, 0, ''));
	}
}
<?php
namespace Df\Core\Helper;
use Df\Core\Format\NounForAmounts;
class Text {
	/**
	 * @used-by df_json_prettify()
	 * @param string $json
	 * @return string
	 */
	public function adjustCyrillicInJson($json) {
		/** @var array(string => string) $trans */
		static $trans = [
			'\u0430'=>'а', '\u0431'=>'б', '\u0432'=>'в', '\u0433'=>'г','\u0434'=>'д'
			, '\u0435'=>'е', '\u0451'=>'ё', '\u0436'=>'ж','\u0437'=>'з', '\u0438'=>'и'
			, '\u0439'=>'й', '\u043a'=>'к','\u043b'=>'л', '\u043c'=>'м'
			, '\u043d'=>'н', '\u043e'=>'о','\u043f'=>'п', '\u0440'=>'р', '\u0441'=>'с'
			, '\u0442'=>'т','\u0443'=>'у', '\u0444'=>'ф', '\u0445'=>'х', '\u0446'=>'ц'
			,'\u0447'=>'ч', '\u0448'=>'ш', '\u0449'=>'щ', '\u044a'=>'ъ','\u044b'=>'ы'
			, '\u044c'=>'ь', '\u044d'=>'э', '\u044e'=>'ю','\u044f'=>'я','\u0410'=>'А'
			, '\u0411'=>'Б', '\u0412'=>'В', '\u0413'=>'Г','\u0414'=>'Д', '\u0415'=>'Е'
			, '\u0401'=>'Ё', '\u0416'=>'Ж','\u0417'=>'З', '\u0418'=>'И', '\u0419'=>'Й'
			, '\u041a'=>'К','\u041b'=>'Л', '\u041c'=>'М', '\u041d'=>'Н', '\u041e'=>'О'
			,'\u041f'=>'П', '\u0420'=>'Р', '\u0421'=>'С', '\u0422'=>'Т','\u0423'=>'У'
			, '\u0424'=>'Ф', '\u0425'=>'Х', '\u0426'=>'Ц','\u0427'=>'Ч', '\u0428'=>'Ш'
			, '\u0429'=>'Щ', '\u042a'=>'Ъ','\u042b'=>'Ы', '\u042c'=>'Ь', '\u042d'=>'Э'
			, '\u042e'=>'Ю','\u042f'=>'Я','\u0456'=>'і', '\u0406'=>'І', '\u0454'=>'є'
			, '\u0404'=>'Є','\u0457'=>'ї', '\u0407'=>'Ї', '\u0491'=>'ґ', '\u0490'=>'Ґ'
		];
		return strtr($json, $trans);
	}

	/**
	 * @param string $string1
	 * @param string $string2
	 * @return bool
	 */
	public function areEqualCI($string1, $string2) {
		return 0 === strcmp(mb_strtolower($string1), mb_strtolower($string2));
	}

	/** @return string */
	public function bom() {return pack('CCC',0xef,0xbb,0xbf);}

	/**
	 * @param string $text
	 * @return string
	 */
	public function bomAdd($text) {
		return (mb_substr($text, 0, 3) === $this->bom()) ? $text : $this->bom() . $text;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function bomRemove($text) {
		df_param_s($text, 0);
		/** @var string $result */
		$result =
			(mb_substr($text, 0, 3) === $this->bom())
			? mb_substr($text, 3)
			: $text
		;
		if (false === $result) {
			$result = '';
		}
		return $result;
	}

	/**
	 * @param string $text
	 * @param int $requiredLength
	 * @param bool $addDots [optional]
	 * @return string
	 */
	public function chop($text, $requiredLength, $addDots = true) {
		df_param_s($text, 0);
		df_param_integer($requiredLength, 1);
		df_param_between($requiredLength, 1, 0);
		df_param_boolean($addDots, 2);
		return
			(mb_strlen($text) <= $requiredLength)
			? $text
			: df_ccc(''
				,$this->trim(mb_substr($text, 0, $requiredLength - ($addDots ? 3 : 0)))
				,$addDots ? '...' : null
			)
		;
	}

	/**
	 * @param string $text
	 * @param bool $needThrow [optional]
	 * @return int|null
	 */
	public function firstInteger($text, $needThrow = true) {
		/** @var int|null $result */
		if (!df_check_string_not_empty($text)) {
			if ($needThrow) {
				df_error('Не могу вычленить целое число из пустой строки.');
			}
			else {
				$result = null;
			}
		}
		else {
			$result = df_preg_match_int('#(\d+)#m', $text, false);
			if (is_null($result) && $needThrow) {
				df_error('Не могу вычленить целое число из строки «%s».', $text);
			}
		}
		return $result;
	}

	/**
	 * @used-by df_day_noun()
	 * @param int $amount
	 * @param array $forms
	 * @return string
	 */
	public function getNounForm($amount, array $forms) {return
		NounForAmounts::s()->getForm(df_param_integer($amount, 0), $forms)
	;}

	/**
	 * http://php.net/manual/function.com-create-guid.php#99425
	 * http://stackoverflow.com/a/26163679
	 * @return string
	 */
	public function guid() {
		return strtolower(
			function_exists('com_create_guid')
			? trim(com_create_guid(), '{}')
			: sprintf(
				'%04X%04X-%04X-%04X-%04X-%04X%04X%04X'
				, mt_rand(0, 65535)
				, mt_rand(0, 65535)
				, mt_rand(0, 65535)
				, mt_rand(16384, 20479)
				, mt_rand(32768, 49151)
				, mt_rand(0, 65535)
				, mt_rand(0, 65535)
				, mt_rand(0, 65535)
			)
		);
	}

	/**
	 * @param string $text
	 * @return bool
	 */
	public function isMultiline($text) {return df_contains($text, "\n") || df_contains($text, "\r");}

	/**
	 * Простой, неполный, но практически адекватный для моих ситуаций
	 * способ опредилелить, является ли строка регулярным выражением.
	 * @param string $text
	 * @return string
	 */
	public function isRegex($text) {return df_starts_with($text, '#');}

	/**
	 * @param string $text
	 * @return bool
	 */
	public function isTranslated($text) {
		if (!isset($this->{__METHOD__}[$text])) {
			/** http://stackoverflow.com/a/16130169 */
			$this->{__METHOD__}[$text] = !is_null(df_preg_match('#[\p{Cyrillic}]#mu', $text, false));
		}
		return $this->{__METHOD__}[$text];
	}

	/**
	 * @param string[] ...$args
	 * @return string|string[]|array(string => string)
	 */
	public function nl2br(...$args) {return df_call_a(function($text) {
		/** @var string $result */
		$text = df_normalize($text);
		/** обрабатываем тег <pre>, который добавляется функцией @see df_xml_output_html() */
		if (!df_contains($text, '<pre class=') && !df_contains($text, '<pre>')) {
			$result  = nl2br($text);
		}
		else {
			$text = str_replace("\n", '{rm-newline}', $text);
			$text = preg_replace_callback(
				'#\<pre(?:\sclass="[^"]*")?\>([\s\S]*)\<\/pre\>#mui'
				, [__CLASS__, 'nl2brCallback']
				, $text
			);
			$result = strtr($text, [
				'{rm-newline}' => '<br/>'
				,'{rm-newline-preserve}' => "\n"
			]);
		}
		return $result;
	}, $args);}

	/**
	 * @param string $name
	 * @return string
	 */
	public function normalizeName($name) {return mb_strtoupper(df_trim($name));}

	/**
	 * @param string $text
	 * @return string[]
	 */
	public function parseTextarea($text) {return df_clean(df_trim(df_explode_n(df_trim($text))));}

	/**
	 * @param string|string[]|array(string => string) $text
	 * @param string $type [optional]
	 * @return string|string[]
	 */
	public function quote($text, $type = self::QUOTE__RUSSIAN) {
		if ('"' === $type) {
			$type = self::QUOTE__DOUBLE;
		}
		else if ("'" === $type) {
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
	public function removeLeadingSpacesMultiline($text, $numSpaces) {return
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
	public function removeLineBreaks($text) {
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
	public function replaceCI($search, $replace, $subject, $count = null) {
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
	public function singleLine($text) {
		/** @var string[] $symbolsToRemove */
		static $symbolsToRemove = ["\r\n", "\r", "\n", "\t"];
		return str_replace($symbolsToRemove, ' ', $text);
	}

	/**
	 * @param string|string[] $text
	 * @param string $charlist [optional]
	 * @return string|string[]
	 */
	public function trim($text, $charlist = null) {
		/** @var string|string $result */
		if (is_array($text)) {
			$result = df_map([$this, __FUNCTION__], $text, $charlist);
		}
		else {
			if (!is_null($charlist)) {
				/** @var string[] $addionalSymbolsToTrim */
				$addionalSymbolsToTrim = ["\n", "\r", ' '];
				foreach ($addionalSymbolsToTrim as $addionalSymbolToTrim) {
					/** @var string $addionalSymbolToTrim */
					if (!df_contains($charlist, $addionalSymbolToTrim)) {
						$charlist .= $addionalSymbolToTrim;
					}
				}
			}
			/**
			 * Обратите внимание, что класс Zend_Filter_StringTrim может работать некорректно
			 * для строк, заканчивающихся заглавной кириллической буквой «Р».
			 * http://framework.zend.com/issues/browse/ZF-11223
			 * Однако решение, которое предложено по ссылке выше
			 * (http://framework.zend.com/issues/browse/ZF-11223)
			 * может приводить к падению интерпретатора PHP
			 * для строк, начинающихся с заглавной кириллической буквы «Р».
			 * Такое у меня происходило в методе @see Df_Autotrading_Model_Request_Locations::parseLocation()
			 * Кто виноват: решение или исходный класс @see Zend_Filter_StringTrim — не знаю
			 * (скорее, решение).
			 * Поэтому мой класс @see \Df\Zf\Filter\StringTrim дополняет решение по ссылке выше
			 * программным кодом из Zend Framework 2.0.
			 */
			/** @var \Df\Zf\Filter\StringTrim $filter */
			$filter = new \Df\Zf\Filter\StringTrim($charlist);
			$result = $filter->filter($text);
			/**
			 * @see Zend_Filter_StringTrim::filter() теоретически может вернуть null,
			 * потому что этот метод зачастую перепоручает вычисление результата функции @uses preg_replace()
			 * @url http://php.net/manual/function.preg-replace.php
			 */
			$result = df_nts($result);
			// Как ни странно, Zend_Filter_StringTrim иногда выдаёт результат « ».
			if (' ' === $result) {
				$result = '';
			}
		}
		return $result;
	}

	/**
	 * Источник алгоритма:
	 * http://stackoverflow.com/a/14338869
	 * @param string $string1
	 * @param string $string2
	 * @return string
	 */
	public function xor_($string1, $string2) {
		return bin2hex(pack('H*', $string1) ^ pack('H*', $string2));
	}
	const QUOTE__DOUBLE = 'double';
	const QUOTE__RUSSIAN = 'russian';
	const QUOTE__SINGLE = 'single';

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * @param string[] $matches
	 * @return string
	 */
	private static function nl2brCallback(array $matches) {
		return str_replace('{rm-newline}', '{rm-newline-preserve}', dfa($matches, 0, ''));
	}
}
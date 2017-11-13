<?php
/**
 * 2016-03-08
 * Добавляет к строке $s окончание $tail,
 * если она в этой строке отсутствует.
 * @param string $s
 * @param string $tail
 * @return string
 */
function df_append($s, $tail) {return df_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * 2015-12-25
 * @param string $text
 * @return string
 */
function df_n_prepend($text) {return '' === $text ? '' : "\n" . $text;}

/**
 * Аналог @see str_pad() для Unicode.
 * http://stackoverflow.com/a/14773638
 * @used-by df_cc_kv()
 * @used-by \Df\Qa\Context::render()
 * @used-by \Df\Qa\State::param()
 * @used-by \Dfe\Moip\CardFormatter::label()
 * @param string $phrase
 * @param int $length
 * @param string $pattern
 * @param int $position
 * @return string
 */
function df_pad($phrase, $length, $pattern = ' ', $position = STR_PAD_RIGHT) {
	/** @var string $result */
	$encoding = 'UTF-8'; /** @var string $encoding */
	$input_length = mb_strlen($phrase, $encoding); /** @var int $input_length */
	$pad_string_length = mb_strlen($pattern, $encoding); /** @var int $pad_string_length */
	if ($length <= 0 || $length - $input_length <= 0) {
		$result = $phrase;
	}
	else {
		$num_pad_chars = $length - $input_length; /** @var int $num_pad_chars */
		/** @var int $left_pad */ /** @var int $right_pad */
		switch ($position) {
			case STR_PAD_RIGHT:
				$left_pad = 0;
				$right_pad = $num_pad_chars;
				break;
			case STR_PAD_LEFT:
				$left_pad = $num_pad_chars;
				$right_pad = 0;
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				df_error();
				break;
		}
		$result = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$result .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
		$result .= $phrase;
		for ($i = 0; $i < $right_pad; ++$i) {
			$result .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
	}
	return $result;
}

/**
 * 2015-11-29
 * Добавляет к строковому представлению целого числа нули слева.
 * 2015-12-01
 * Строковое представление может быть 16-ричным (код цвета), поэтому убрал @see df_int()
 * http://stackoverflow.com/a/1699980
 * @param int $length
 * @param int|string $number
 * @return string
 */
function df_pad0($length, $number) {return str_pad($number, $length, '0', STR_PAD_LEFT);}

/**
 * 2016-03-08
 * Добавляет к строке $s приставку $head,
 * если она в этой строке отсутствует.
 * @param string $s
 * @param string $head
 * @return string
 */
function df_prepend($s, $head) {return df_starts_with($s, $head) ? $s : $head . $s;}

/**
 * @param string[] ...$args
 * @return string|string[]|array(string => string)
 */
function df_tab(...$args) {return df_call_a(function($text) {return "\t" . $text;}, $args);}

/**
 * @param string $text
 * @return string
 */
function df_tab_multiline($text) {return df_cc_n(df_tab(df_explode_n($text)));}
<?php
/**
 * 2016-03-08 It adds the $tail suffix to the $s string if the suffix is absent in $s.
 * @used-by df_cc_path_t()
 * @used-by df_file_ext_add()
 */
function df_append(string $s, string $tail):string {return df_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * 2015-12-25
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 */
function df_n_prepend(string $s):string {return df_es($s) ? $s : "\n$s";}

/**
 * Аналог @see str_pad() для Unicode: http://stackoverflow.com/a/14773638
 * @used-by df_kv()
 * @used-by \Dfe\Moip\CardFormatter::label()
 */
function df_pad(string $phrase, int $length, string $pattern = ' ', int $position = STR_PAD_RIGHT):string {/** @var string $r */
	$encoding = 'UTF-8'; /** @var string $encoding */
	$input_length = mb_strlen($phrase, $encoding); /** @var int $input_length */
	$pad_string_length = mb_strlen($pattern, $encoding); /** @var int $pad_string_length */
	if ($length <= 0 || $length - $input_length <= 0) {
		$r = $phrase;
	}
	else {
		$num_pad_chars = $length - $input_length; /** @var int $num_pad_chars */
		/** @var int $left_pad */ /** @var int $right_pad */
		switch ($position) {
			case STR_PAD_RIGHT:
				# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
				[$left_pad, $right_pad] = [0, $num_pad_chars];
				break;
			case STR_PAD_LEFT:
				# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
				[$left_pad, $right_pad] = [$num_pad_chars, 0];
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				df_error();
		}
		$r = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
		$r .= $phrase;
		for ($i = 0; $i < $right_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
	}
	return $r;
}

/**
 * 2015-11-29 Добавляет к строковому представлению целого числа нули слева.
 * 2015-12-01
 * Строковое представление может быть 16-ричным (код цвета), поэтому убрал @see df_int()
 * http://stackoverflow.com/a/1699980
 * @used-by df_rgb2hex()
 */
function df_pad0(int $length, string $number):string {return str_pad($number, $length, '0', STR_PAD_LEFT);}

/**
 * 2016-03-08 It adds the $head prefix to the $s string if the prefix is absent in $s.
 * @used-by df_sys_path_abs()
 * @used-by ikf_ite()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 */
function df_prepend(string $s, string $head):string {return df_starts_with($s, $head) ? $s : $head . $s;}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_tab_multiline()
 * @param string|string[] ...$a
 * @return string|string[]|array(string => string)
 */
function df_tab(...$a) {return df_call_a($a, function(string $s):string {return "\t" . $s;});}

/**
 * @used-by \Df\Core\Html\Tag::content()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Typography\Css::render()
 */
function df_tab_multiline(string $s):string {return df_cc_n(df_tab(df_explode_n($s)));}
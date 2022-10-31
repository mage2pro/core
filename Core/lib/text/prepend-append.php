<?php
/**
 * 2016-03-08 It adds the $tail suffix to the $s string if the suffix is absent in $s.
 * @used-by df_cc_path_t()
 * @used-by df_file_ext_add()
 * @param string $s
 * @param string $tail
 */
function df_append($s, $tail):string {return df_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * 2015-12-25
 * @used-by \Dfe\Frontend\Block\ProductView\Css::_toHtml()
 * @param string $s
 */
function df_n_prepend($s):string {return '' === $s ? '' : "\n$s";}

/**
 * Аналог @see str_pad() для Unicode: http://stackoverflow.com/a/14773638
 * @used-by df_kv()
 * @used-by \Df\Qa\Trace\Formatter::param()
 * @used-by \Dfe\Moip\CardFormatter::label()
 * @param string $phrase
 * @param int $length
 * @param string $pattern
 * @param int $position
 */
function df_pad($phrase, $length, $pattern = ' ', $position = STR_PAD_RIGHT):string {/** @var string $r */
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
				list($left_pad, $right_pad) = [0, $num_pad_chars];
				break;
			case STR_PAD_LEFT:
				list($left_pad, $right_pad) = [$num_pad_chars, 0];
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				df_error();
				break;
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
 * @param int $length
 * @param int|string $number
 */
function df_pad0($length, $number):string {return str_pad($number, $length, '0', STR_PAD_LEFT);}

/**
 * 2016-03-08 It adds the $head prefix to the $s string if the prefix is absent in $s.
 * @used-by df_path_absolute()
 * @used-by ikf_ite()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 * @param string $s
 * @param string $head
 */
function df_prepend($s, $head):string {return df_starts_with($s, $head) ? $s : $head . $s;}

/**
 * @used-by df_tab_multiline()
 * @param string|string[] ...$args
 * @return string|string[]|array(string => string)
 */
function df_tab(...$args) {return df_call_a(function($text) {return "\t" . $text;}, $args);}

/**
 * @used-by \Df\Core\Format\Html\Tag::content()
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Qa\Dumper::dumpArray()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @used-by \Df\Typography\Css::render()
 * @param string $s
 */
function df_tab_multiline($s):string {return df_cc_n(df_tab(df_explode_n($s)));}
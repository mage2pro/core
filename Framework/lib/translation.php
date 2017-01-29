<?php
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Phrase;
/**
 * 2016-07-14
 * @param string|Phrase $text
 * @return Phrase
 */
function df_phrase($text) {return $text instanceof Phrase ? $text : __($text);}

/**
 * 2015-09-29
 * @used-by df_map_to_options_t()
 * @param string[] $strings
 * @param bool $now [optional]
 * @return string[]
 */
function df_translate_a($strings, $now = false) {
	/** @var string[] $result */
	$result = array_map('__', $strings);
	if ($now) {
		/**
		 * Иногда нужно перевести строки именно сейчас,
		 * чтобы не выпасть из контекста перевода.
		 * @see \Dfr\Translation\Realtime\Dictionary
		 */
		$result = array_map('strval', $result);
	}
	return $result;
}

/**
 * 2016-10-31
 * @param string $string
 * @return string
 */
function df_translit_url($string) {return df_translit_url_m()->filter($string);}

/**
 * 2016-10-31
 * @return TranslitUrl
 */
function df_translit_url_m() {return df_o(TranslitUrl::class);}
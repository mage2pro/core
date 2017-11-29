<?php
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
		 * @see \Dfr\Core\Realtime\Dictionary
		 */
		$result = array_map('strval', $result);
	}
	return $result;
}

/**
 * 2017-02-09
 * Делаем всё то же, что и @see \Magento\Framework\Filter\TranslitUrl::filter(),
 * но без приведения к нижнему регистру.
 * '歐付寶 all/Pay' => 'all-Pay'
 * Если нужно приведение к нижнему регистру, то используйте @see df_translit_url_lc().
 *
 * Пример №1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  all/Pay
 * @see df_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Пример №2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  allPay
 * @see df_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * @used-by df_translit_url_lc()
 * @used-by \Df\Sentry\Client::tags_context()
 * @param string $s
 * @return string
 */
function df_translit_url($s) {return trim(preg_replace('#[^0-9a-z]+#i', '-', df_translit($s)), '-');}

/**
 * 2016-10-31
 * 2017-02-09
 * В настоящее время никем не используется.
 * '歐付寶 all/Pay' => 'all-pay'
 *
 * Пример №1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  all/Pay
 * @see df_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Пример №2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  allPay
 * @see df_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * @param string $s
 * @return string
 */
function df_translit_url_lc($s) {return strtolower(df_translit_url($s));}
<?php
/**
 * 2015-02-17
 * Не используем методы ядра
 * @see Mage_Core_Helper_Abstract::escapeHtml()
 * @see Mage_Core_Helper_Abstract::htmlEscape()
 * потому что они используют @uses htmlspecialchars() со вторым параметром @see ENT_COMPAT,
 * в результате чего одиночные кавычки не экранируются.
 * Ядро Magento не использует одиночные кавычки при формировании HTML
 * (в частности, в шаблонах *.phtml), поэтому, видимо, их устраивает режим ENT_COMPAT.
 * Российская сборка Magento использует при формировании HTML одиночные кавычки,
 * поэтому нам нужен режим ENT_QUOTES.
 * Это важно, например, в методе @used-by Df_Core_Model_Format_Html_Tag::getAttributeAsText()
 * @see df_ejs()
 * @used-by Dfe_Stripe/view/frontend/templates/multishipping.phtml
 * @param string[] ...$args
 * @return string|string[]
 */
function df_e(...$args) {return df_call_a(function($text) {return
	htmlspecialchars($text, ENT_QUOTES, 'UTF-8', $double_encode = false)
;}, $args);}

/**
 * @param string $text
 * @return string
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 */
function df_normalize($text) {return strtr($text, ["\r\n" => "\n", "\r" => "\n"]);}

/**
 * 2017-04-22 «+79.6-2» => «7962»
 * http://stackoverflow.com/a/35619532
 * @param string $s
 * @return string
 */
function df_remove_non_digits($s) {return preg_replace('[\D]', '', $s);}

/**
 * 2015-03-03
 * Раньше алгоритм был таким:
 	 strtr($s, array_fill_keys($wordsToRemove, ''))
 * Он корректен, но новый алгоритм быстрее, потому что не требует вызова нестандартных функций.
 * http://php.net/str_replace
 * «If replace has fewer values than search,
 * then an empty string is used for the rest of replacement values.»
 * http://3v4l.org/9qvC4
 * @used-by df_phone_explode()
 * @used-by \Dfe\IPay88\Signer::adjust()
 * @param string $s
 * @param string[] $remove
 * @return string
 */
function df_string_clean($s, ...$remove) {return str_replace(dfa_flatten($remove), null, $s);}


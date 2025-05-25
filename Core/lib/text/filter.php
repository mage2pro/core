<?php
/**
 * 2015-02-17
 * Не используем методы ядра
 * @see Mage_Core_Helper_Abstract::escapeHtml()
 * @see Mage_Core_Helper_Abstract::htmlEscape()
 * потому что они используют @uses htmlspecialchars() со вторым параметром @see ENT_COMPAT,
 * в результате чего одиночные кавычки не экранируются.
 * Ядро Magento не использует одиночные кавычки при формировании HTML
 * (в частности, в шаблонах *.phtml), поэтому, видимо, их устраивает режим  @see ENT_COMPAT.
 * Российская сборка Magento использует при формировании HTML одиночные кавычки, поэтому нам нужен режим @see ENT_QUOTES.
 * Это важно, например, в методе @used-by Df_Core_Model_Format_Html_Tag::getAttributeAsText()
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_ejs()
 * @used-by cs_quote_description() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/107)
 * @used-by Dfe_Stripe/view/frontend/templates/multishipping.phtml
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_e(...$a) {return df_call_a(function(string $s):string {return htmlspecialchars(
	$s, ENT_QUOTES, 'UTF-8', false
);}, $a);}

/**
 * 2017-04-22
 * «+79.6-2» => «7962»
 * http://stackoverflow.com/a/35619532
 * 2022-10-31 @deprecated It is unused.
 */
function df_remove_non_digits(string $s):string {return preg_replace('[\D]', '', $s);}

/**
 * 2015-03-03
 * Раньше алгоритм был таким:
 *	 	strtr($s, array_fill_keys($wordsToRemove, ''))
 * Он корректен, но новый алгоритм быстрее, потому что не требует вызова нестандартных функций.
 * https://php.net/str_replace
 * «If replace has fewer values than search, then an empty string is used for the rest of replacement values.»
 * http://3v4l.org/9qvC4
 * 2022-11-26
 * We can not declare the argument as `string ...$remove` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_phone_explode()
 * @used-by Dfe\IPay88\Signer::adjust()
 * @param string|string[] ...$remove
 */
function df_string_clean(string $s, ...$remove):string {return str_replace(dfa_flatten($remove), null, $s);}

/**
 * 2017-02-09
 * '歐付寶 all/Pay' => 'all/Pay'
 *
 * Example #1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  all/Pay
 * @see df_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Example #2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  allPay
 * @see df_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * 2017-11-13
 * Note 1.
 * Previously, I used @see \Magento\Framework\Filter\Translit::filter() here:
 * 		$m = df_o(Translit::class); return $m->filter($s);
 * https://github.com/mage2pro/core/blob/3.3.9/Framework/lib/translation.php#L32-L56
 * But now I decided to switch to @uses transliterator_transliterate(),
 * because it produces better results for me.
 *
 * $m = df_o(Translit::class);
 * echo $m->filter('歐付寶 Rónán allPay Федюк [] --');
 * Output: «Ronan allPay fedjuk [] --»
 *
 * echo transliterator_transliterate('Any-Latin; Latin-ASCII', '歐付寶 Rónán allPay Федюк [] --')
 * Output: «ou fu bao Ronan allPay Feduk [] --»
 *
 * Note 2.
 * I have already implemented a similar algorithm in JavaScript:
 * https://github.com/mage2pro/core/blob/3.2.9/Payment/view/frontend/web/card.js#L188-L201
 *		baChange(this, function(a) {this.cardholder((a.firstname + ' ' + a.lastname).toUpperCase()
 *			.normalize('NFD').replace(/[^\w\s-]/g, '')
 *		);});
 * https://github.com/mage2pro/core/issues/37#issuecomment-337546667
 *
 * Note 3.
 * I have adapted an implementation from here: https://stackoverflow.com/questions/3371697#comment63507856_3371773
 *
 * @used-by df_translit_url()
 * @used-by Dfe\AlphaCommerceHub\Charge::textFilter()
 * @used-by Dfe\AlphaCommerceHub\Charge::textFilterStreet()
 * @used-by Dfe\Stripe\Block\Multishipping::cardholder()
 * @used-by Dfe\TBCBank\Charge::textFilter()
 * @used-by Dfe\ZoomVe\OriginCityLocator::p() (https://github.com/mage2pro/zoom-ve/issues/7)
 */
function df_translit(string $s):string {return transliterator_transliterate('Any-Latin; Latin-ASCII', $s);}
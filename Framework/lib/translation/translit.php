<?php
/**
 * 2017-02-09
 * It does the same as @see \Magento\Framework\Filter\TranslitUrl::filter(), but without lower-casing:
 * '歐付寶 all/Pay' => 'all-Pay'
 * If you need lower-casing, then use @see df_translit_url_lc() instead.
 * Example #1: '歐付寶 all/Pay':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  all/Pay
 * 		@see df_translit_url => all-Pay
 * 		@see df_translit_url_lc => all-pay
 * Example #2: '歐付寶 O'Pay (allPay)':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  allPay
 * 		@see df_translit_url => allPay
 * 		@see df_translit_url_lc => allpay
 * @used-by df_translit_url_lc()
 * @used-by Df\Sentry\Client::tags()
 */
function df_translit_url(string $s):string {return trim(preg_replace('#[^0-9a-z]+#i', '-', df_translit($s)), '-');}

/**
 * 2016-10-31
 * 2017-02-09
 * @deprecated It is unused.
 * '歐付寶 all/Pay' => 'all-pay'
 * Example #1: '歐付寶 all/Pay':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  all/Pay
 * 		@see df_translit_url => all-Pay
 * 		@see df_translit_url_lc => all-pay
 * Example #2: '歐付寶 O'Pay (allPay)':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  allPay
 * 		@see df_translit_url => allPay
 * 		@see df_translit_url_lc => allpay
 */
function df_translit_url_lc(string $s):string {return strtolower(df_translit_url($s));}
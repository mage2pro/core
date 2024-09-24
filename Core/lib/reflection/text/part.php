<?php
/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_class_my()
 * @param string|object $c
 */
function df_class_f($c):string {return df_first(df_explode_class($c));}

/**
 * 2015-12-29
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_class_llc()
 * @used-by Df\API\Facade::path()
 * @used-by Df\Payment\W\F::aspect()
 * @used-by Df\Qa\Trace\Formatter::p()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\BankCard\CancelPayment::t01()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\BankCard\CapturePayment::t01()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\BankCard\RefundPayment::t01()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\PayPal\CapturePayment::t01()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\PayPal\PaymentStatus::t01()
 * @used-by Dfe\AlphaCommerceHub\Test\CaseT\PayPal\RefundPayment::t01()
 * @param string|object $c
 */
function df_class_l($c):string {return df_last(df_explode_class($c));}

/**
 * 2018-01-30
 * 2021-10-27 @deprecared It is unused.
 * @used-by CabinetsBay\Catalog\B\Featured::p() (https://github.com/cabinetsbay/catalog/issues/27)
 * @param string|object $c
 */
function df_class_llc($c):string {return strtolower(df_class_l($c));}

/**
 * 2016-02-09
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * https://3v4l.org/k6Hd5
 * @used-by Df\API\Settings::titleB()
 * @param string|object $c
 */
function df_class_second($c):string {return df_explode_class($c)[1];}

/**
 * 2016-02-09
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_oi_get()
 * @used-by df_oi_save()
 * @param string|object $c
 */
function df_class_second_lc($c):string {return df_lcfirst(df_class_second($c));}

/**
 * 2016-11-25 «Df\Sso\Settings\Button» => «Settings\Button»
 * 2017-02-11 «Df\Sso\Settings\IButton» => «Settings\Button»
 * @used-by dfs_con()
 * @param string|object $c
 */
function df_class_suffix($c):string {/** @var string $r */
	$r = implode(df_cld($c), array_slice(df_explode_class($c), 2));
	if (interface_exists($c)) {
		if ($a = df_explode_class($r)) {/** @var string[] $a */
			$len = count($a); /** @var int $len */
			$last = $a[$len - 1]; /** @var string $last */
			$a[$len - 1] = 'I' !== $last[0] ? $last : substr($last, 1);
			$r = df_cc_class($a);
		}
	}
	return $r;
}
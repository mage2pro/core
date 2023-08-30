<?php
use Df\Core\Exception as DFE;
use Df\Payment\Currency as C;
use Df\Payment\Method as M;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
/**
 * 2017-10-12               
 * @used-by dfpex_from_doc()
 * @used-by \Df\Payment\ConfigProvider::currency()
 * @used-by \Df\Payment\Method::cPayment()
 * @used-by \Df\Payment\Method::isAvailable()
 * @used-by \Df\Payment\Operation\Source::currencyC()
 * @param string|object $m
 */
function dfp_currency($m):C {return C::f($m);}

/**
 * 2017-04-08
 * @used-by dfpex_from_doc()
 * @param mixed $a0
 * @param mixed|null $a1 [optional]
 * @return array(M, O|Q)  
 * @throws DFE
 */
function dfpex_args($a0, $a1 = null):array {return ($a1
	? ($a0 instanceof M ? [$a0, df_oq($a1)] : (df_is_oq($a0) ? [df_ar($a1, M::class), $a0] : []))
	: ($a0 instanceof M ? [$a0, $a0->o()] : (df_is_oq($a0) ? [dfpm($a0), $a0] : []))) ?: df_error(
		'dfpex_args(): invalid first argument: %s.', df_type($a0)
	)
;}

/**
 * 2017-04-08
 * Converts $a from a sales document currency to the payment currency.
 * The payment currency is usually set here: «Mage2.PRO» → «Payment» → <...> → «Payment Currency».
 * @used-by \Df\Payment\Operation\Source::cFromDoc()
 * @param mixed ...$args
 */
function dfpex_from_doc(float $a, ...$args):float {
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($m, $doc) = dfpex_args(...$args); /** @var M $m */ /** @var Q|O $doc */
	return dfp_currency($m)->fromOrder($a, $doc);
}
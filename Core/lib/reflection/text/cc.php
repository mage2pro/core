<?php
/**
 * 2016-02-08 Применение @uses dfa_flatten() делает возможным вызовы типа: df_cc_class_uc('Aa', ['Bb', 'Cb']) => Aa\Bb\Cb
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_cc_class_uc()
 * @used-by df_cc_class_uc()
 * @used-by df_class_suffix()
 * @used-by df_con_hier_suf()
 * @used-by df_con_hier_suf_ta()
 * @used-by \Df\Payment\W\F::c()
 * @used-by \Dfe\AllPay\Method::getInfoBlockType()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @param string|string[] $a
 */
function df_cc_class(...$a):string {return implode('\\', df_clean(dfa_flatten($a)));}

/**
 * 2016-03-25
 * Применение @uses dfa_flatten() делает возможным вызовы типа: `df_cc_class_uc('aa', ['bb', 'cc'])` => Aa\Bb\Cc
 * Мы используем это в модулях Stripe и Checkout.com.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_cc_class()
 * @used-by df_con_hier_suf_ta()
 * @used-by \Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 * @used-by \Df\Payment\W\F::try_()
 * @used-by \Dfe\CheckoutCom\Handler::p()
 * @used-by \Dfe\Moip\Method::getInfoBlockType()
 * @param string|string[] $a
 */
function df_cc_class_uc(...$a):string {return df_cc_class(df_ucfirst(dfa_flatten($a)));}

/**
 * 2016-08-10 Если класс не указан, то вернёт название функции. Поэтому в качестве $a1 можно передавать `null`.
 * @used-by df_caller_m()
 * @used-by df_caller_mf()
 * @used-by df_rest_action()
 * @used-by \Df\Qa\Trace\Frame::method()
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 */
function df_cc_method($a1, $a2 = null):string {return df_ccc('::',
	$a2 ? [df_cts($a1), $a2] : (
		!isset($a1['function']) ? $a1 :
			[dfa($a1, 'class'), $a1['function']]
	)
);}
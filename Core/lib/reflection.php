<?php
use Df\Core\Exception as DFE;
use Df\Core\R\ConT;
use ReflectionClass as RC;
/**
 * 2016-02-08
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('Aa', ['Bb', 'Cb']) => Aa\Bb\Cb
 * @see df_cc_class_uc()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class(...$args) {return implode('\\', df_clean(dfa_flatten($args)));}

/**
 * 2016-03-25
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('aa', ['bb', 'cc']) => Aa\Bb\Cc
 * Мы используем это в модулях Stripe и Checkout.com.
 * @see df_cc_class()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class_uc(...$args) {return df_cc_class(df_ucfirst(dfa_flatten($args)));}

/**
 * 2016-08-10
 * Если класс не указан, то вернёт название функции.
 * Поэтому в качестве $a1 можно передавать null.
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 * @return string
 */
function df_cc_method($a1, $a2 = null) {return df_ccc('::',
	$a2 ? [df_cts($a1), $a2] :
		(!isset($a1['function']) ? $a1 :
			[dfa($a1, 'class'), $a1['function']]
		)
);}

/**
 * 2017-01-11
 * http://stackoverflow.com/a/666701
 * @used-by \Df\Payment\W\F::i()
 * @param string $c
 * @return bool
 */
function df_class_check_abstract($c) {df_param_sne($c, 0); return (new RC(df_ctr($c)))->isAbstract();}

/**
 * 2016-05-06
 * By analogy with https://github.com/magento/magento2/blob/135f967/lib/internal/Magento/Framework/ObjectManager/TMap.php#L97-L99
 * 2016-05-23
 * Намеренно не объединяем строки в единное выражение, чтобы собака @ не подавляла сбои первой строки.
 * Такие сбои могут произойти при синтаксических ошибках в проверяемом классе
 * (похоже, getInstanceType как-то загружает код класса).
 * @used-by dfpm_c()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 * @param string $c
 * @return bool
 */
function df_class_exists($c) {$c = df_ctr($c); return @class_exists($c);}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_class_f($c) {return df_first(df_explode_class($c));}

/**
 * 2017-01-10
 * @uses df_cts() отсекает окончание «\Interceptor»: без этого функция работала бы не так, как мы хотим
 * (возвращала бы путь к файлу из папки «var/generation», а не из папки модуля).
 * Пример результата: «C:/work/mage2.pro/store/vendor/mage2pro/allpay/Webhook/ATM.php».
 * Пока эта функция никем не используется.
 * @param string|object $c
 * @return string
 */
function df_class_file($c) {return df_path_n((new RC(df_cts(df_ctr($c))))->getFileName());}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @used-by df_class_llc()
 * @used-by \Df\API\Facade::path()
 * @used-by \Df\Payment\W\F::aspect()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\BankCard\CancelPayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\BankCard\CapturePayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\BankCard\RefundPayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\PayPal\CapturePayment::t01()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\PayPal\PaymentStatus::t01()
 * @used-by \Dfe\AlphaCommerceHub\T\CaseT\PayPal\RefundPayment::t01()
 * @param string|object $c
 * @return string
 */
function df_class_l($c) {return df_last(df_explode_class($c));}

/**
 * 2018-01-30
 * @param string|object $c
 * @return string
 */
function df_class_llc($c) {return strtolower(df_class_l($c));}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return bool
 */
function df_class_my($c) {return in_array(df_class_f($c), ['Df', 'Dfe', 'Dfr']);}

/**
 * 2016-07-10
 * Df\PaypalClone\W\Handler => Df\PaypalClone\Request
 * @param string|object $c
 * @param string[] $newSuffix
 * @return string
 */
function df_class_replace_last($c, ...$newSuffix) {return implode(df_cld($c),
	array_merge(df_head(df_explode_class($c)), dfa_flatten($newSuffix))
);}

/**
 * 2016-02-09
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_class_second($c) {return df_explode_class($c)[1];}

/**
 * 2016-02-09
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_oi_get()
 * @used-by df_oi_save()
 * @param string|object $c
 * @return string
 */
function df_class_second_lc($c) {return df_lcfirst(df_class_second($c));}

/**
 * 2016-11-25 «Df\Sso\Settings\Button» => «Settings\Button»
 * 2017-02-11 «Df\Sso\Settings\IButton» => «Settings\Button»  
 * @used-by dfs_con()
 * @param string|object $c
 * @return string
 */
function df_class_suffix($c) {
	/** @var string $result */
	$result = implode(df_cld($c), array_slice(df_explode_class($c), 2));
	if (interface_exists($c)) {
		/** @var string[] $a */
		if ($a = df_explode_class($result)) {
			/** @var int $len */
			$len = count($a);
			/** @var string $last */
			$last = $a[$len - 1];
			$a[$len - 1] = 'I' !== $last[0] ? $last : substr($last, 1);
			$result =  df_cc_class($a);
		}
	}
	return $result;
}

/**
 * 2016-10-15
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * @param string|object $c
 * @return string
 */
function df_cld($c) {return df_contains(df_cts($c) , '\\') ? '\\' : '_';}

/**
 * 2016-08-04
 * 2016-08-10
 * @uses defined() не реагирует на методы класса, в том числе на статические,
 * поэтому нам использовать эту функию безопасно: https://3v4l.org/9RBfr
 * @used-by \Df\Config\O::ct()
 * @used-by \Df\Payment\Method::codeS()
 * @param string|object $c
 * @param string $name
 * @param mixed|callable $def [optional]
 * @return mixed
 */
function df_const($c, $name, $def = null) {
	/** @var string $nameFull */
	$nameFull = df_cts($c) . '::' . $name;
	return defined($nameFull) ? constant($nameFull) : df_call_if($def);
}

/**
 * 2016-02-08
 * Проверяет наличие следующих классов в указанном порядке:
 * 1) <имя конечного модуля>\<окончание класса>
 * 2) $def
 * Возвращает первый из найденных классов.
 * @param object|string $c
 * $c could be:
 * 1) A class name: «A\B\C».
 * 2) An object. It is reduced to case 1 via @see get_class()
 * @used-by dfs_con()
 * @used-by \Df\Framework\Mail\TransportObserver::execute()
 * @param string|string[] $suf
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con($c, $suf, $def = null, $throw = true) {return ConT::generic(
	function($c, $suf) {return
		/** @var string $del */
		// 2016-11-25
		// Применение df_cc() обязательно, потому что $suf может быть массивом.
		df_cc($del = df_cld($c), df_module_name($c, $del), $suf)
	;}, $c, $suf, $def, $throw
);}

/**
 * 2016-10-26
 * @param object|string $c
 * @param string|string[] $suf
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_child($c, $suf, $def = null, $throw = true) {return ConT::generic(
	function($c, $suf) {return
		df_cc(df_cld($c), $c, $suf)
	;}, $c, $suf, $def, $throw
);}

/**
 * 2016-11-25
 * Возвращает имя класса с тем же суффиксом, что и $def,
 * но из папки того же модуля, которому принадлежит класс $c.
 * Результат должен быть наследником класса $def.
 * Если класс не найден, то возвращается $def.
 * Параметр $throw этой функции не нужен, потому что параметр $def обязателен.
 * Пример:
 * $c => \Dfe\FacebookLogin\Button
 * $def = \Df\Sso\Settings\Button
 * Результат: «Dfe\FacebookLogin\Settings\Button»
 *
 * 2016-12-28
 * Отличие от @see df_con_sibling рассмотрим на примере:
 * класс: Dfe\AAA\Webhook\Exception
 * df_con_heir($this, \Df\Payment\Xxx\Yyy::class)
 * 		ищет сначала \Dfe\AAA\Xxx\Yyy
 * 		если не найдено — возвращает \Df\Payment\Xxx\Yyy
 * df_con_sibling($this, 'Xxx\Yyy', \Df\Payment\Xxx\Yyy)
 * 		работает точно так же, но запись длиннее
 * 		+ не проверяет, что результат имеет класс \Df\Payment\Xxx\Yyy или его потомка.

 * 2017-02-11 Отныне функция позволяет в качестве $def передавать интерфейс: @see df_class_suffix()
 *
 * @used-by dfpm_c()
 * @used-by dfsm_c()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\GingerPaymentsBase\Settings::os()
 * @used-by \Df\Payment\Currency::f()
 * @used-by \Df\Payment\Facade::s()
 * @used-by \Df\PaypalClone\Charge::p()
 * @used-by \Df\Sso\Button::s()
 * @used-by \Df\Sso\CustomerReturn::c()
 * @used-by \Df\StripeClone\Facade\Card::create()
 * @used-by \Df\StripeClone\P\Charge::sn()
 * @used-by \Df\StripeClone\P\Preorder::request()
 * @used-by \Df\StripeClone\P\Reg::request()
 * @used-by \Df\Zoho\API\Client::i()
 * @param object|string $c
 * @param string $def
 * @return string|null
 */
function df_con_heir($c, $def) {return df_ar(
	df_con(df_module_name_c($c), df_class_suffix($def), $def), $def
);}

/**
 * 2017-01-04
 * Сначала ищет класс с суффиксом, как у $ar, в папке класса $c,
 * а затем спускается по иерархии наследования для $c,
 * и так до тех пор, пока не найдёт в папке предка класс с суффиксом, как у $ar.
 * 2017-02-11 Отныне функция позволяет в качестве $ar передавать интерфейс: @see df_class_suffix()
 * @used-by \Df\Config\Settings::convention()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\Init\Action::sg()
 * @used-by \Df\Payment\Method::getFormBlockType()
 * @used-by \Df\Payment\Method::getInfoBlockType()
 * @used-by \Df\Payment\Method::s()
 * @used-by \Df\Payment\Url::f()
 * @used-by \Df\Payment\W\F::__construct()
 * @used-by \Df\Payment\W\F::s()
 * @used-by \Df\Shipping\Method::s()
 * @param object|string $c
 * @param string $ar
 * @param bool $throw [optional]
 * @return string|null
 * @throws DFE
 */
function df_con_hier($c, $ar, $throw = true) {/** @var string|null $r */ return
	($r = df_con_hier_suf($c, df_class_suffix($ar), $throw)) ? df_ar($r, $ar) : null
;}

/**
 * 2017-03-11
 * @used-by df_con_hier()
 * @param object|string $c
 * @param string $suf
 * @param bool $throw [optional]
 * @return string|null
 * @throws DFE
 */
function df_con_hier_suf($c, $suf, $throw = true) {
	/** @var string|null $r */
	if (!($r = df_con($c, $suf, null, false))) {
		// 2017-01-11 Используем df_cts(), чтобы отсечь окончание «\Interceptor».
		/** @var string|false $parent */
		if ($parent = get_parent_class(df_cts($c))) {
			$r = df_con_hier_suf($parent, $suf, $throw);
		}
		elseif ($throw) {
			/** @var string $expected */
			df_error('df_con_hier_suf(): %s.',
				!df_class_exists($expected = df_cc_class(df_module_name_c($c), $suf))
				? "ascended to the absent class «{$expected}»"
				: (df_class_check_abstract($expected) ? "ascended to the abstract class «{$expected}»" :
					"unknown error"
				)
			);
		}
	}
	return $r;
}

/**
 * 2017-03-20
 * Сначала проходит по иерархии суффиксов, и лишь затем — по иерархии наследования.
 * @param object|string $c
 * @param string|string[] $sufBase
 * @param string|string[] $ta
 * @param bool $throw [optional]
 * @return string|null
 * @throws DFE
 */
function df_con_hier_suf_ta($c, $sufBase, $ta, $throw = true) {
	$ta = df_array($ta);
	$sufBase = df_cc_class($sufBase);
	$result = null; /** @var string|null $result */
	$taCopy = $ta; /** @var string[] $taCopy */
	$count = count($ta); /** @var int $count */
	while (-1 < $count && !($result = df_con($c, df_cc_class_uc($sufBase, $ta), null, false))) {
		array_pop($ta); $count--;
	}
	// 2017-01-11
	// Используем df_cts(), чтобы отсечь окончание «\Interceptor».
	/** @var string|false $parent */
	if (!$result && ($parent = get_parent_class(df_cts($c)))) {
		$result = df_con_hier_suf_ta($parent, $sufBase, $taCopy, $throw);
	}
	return $result || !$throw ? $result :
		df_error("The «%s» class is required.", df_cc_class_uc(df_module_name_c($c), $sufBase, $ta))
	;
}

/**
 * 2016-08-29
 * @used-by dfpm_call_s()
 * @used-by dfsm_call_s()
 * @used-by \Df\StripeClone\Method::chargeNew()
 * @param string|object $c
 * @param string|string[] $suffix
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function df_con_s($c, $suffix, $method, array $params = []) {return dfcf(
	function($c, $suffix, $method, array $params = []) {
		$class = df_con($c, $suffix); /** @var string $class */
		if (!method_exists($class, $method)) {
			df_error("The class {$class} should define the method «{$method}».");
		}
		return call_user_func_array([$class, $method], $params);
	}
, func_get_args());}

/**
 * 2016-07-10          
 * 2016-11-25
 * Возвращает имя класса из той же папки, что и $c, но с окончанием $nameLast.
 * Пример:
 * $c => \Df\Payment\W\Handler
 * $nameLast = «Exception»
 * Результат: «Df\Payment\W\Handler\Exception»
 * @used-by \Df\Payment\W\Handler::exceptionC()
 * 2016-12-28
 * Отличие от @see df_con_heir рассмотрим на примере:
 * класс: Dfe\AAA\Webhook\Exception
 * df_con_heir($this, \Df\Payment\Xxx\Yyy::class)
 * 		ищет сначала \Dfe\AAA\Xxx\Yyy
 * 		если не найдено — возвращает \Df\Payment\Xxx\Yyy
 * df_con_sibling($this, 'Webhook\Report', \Df\Payment\Xxx\Yyy)
 * 		работает точно так же, но запись длиннее
 * 		+ не проверяет, что результат имеет класс \Df\Payment\Xxx\Yyy или его потомка.
 * 
 * @param object|string $c
 * @param string|string[] $nameLast
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_sibling($c, $nameLast, $def = null, $throw = true) {return ConT::generic(
	function($c, $nameLast) {return
		df_class_replace_last($c, $nameLast)
	;}, $c, $nameLast, $def, $throw
);}

/**
 * 2015-08-14
 * Обратите внимание, что @uses get_class() не ставит «\» впереди имени класса:
 * http://3v4l.org/HPF9R
	namespace A;
	class B {}
	$b = new B;
	echo get_class($b);
 * => «A\B»
 *
 * 2015-09-01
 * Обратите внимание, что @uses ltrim() корректно работает с кириллицей:
 * https://3v4l.org/rrNL9
 * echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 *
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * @used-by df_explode_class()
 * @used-by df_module_name()
 * @used-by dfsm_code()
 * @used-by \Df\Payment\Method::getInfoBlockType()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_cts($c, $del = '\\') {
	/** @var string $result */
	$result = df_trim_text_right(is_object($c) ? get_class($c) : ltrim($c, '\\'), '\Interceptor');
	return '\\' === $del ? $result : str_replace('\\', $del, $result);
}

/**
 * 2016-01-29
 * @param string $c
 * @param string $del
 * @return string
 */
function df_cts_lc($c, $del) {return implode($del, df_explode_class_lc($c));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => dfe_checkout_com
 * @param string $c
 * @param string $del
 * @return string
 */
function df_cts_lc_camel($c, $del) {return implode($del, df_explode_class_lc_camel($c));}

/**
 * @param string|object $c
 * @return string[]
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 */
function df_explode_class($c) {return df_explode_multiple(['\\', '_'], df_cts($c));}

/**
 * 2016-04-11
 * Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_camel($c) {return dfa_flatten(df_explode_camel(explode('\\', df_cts($c))));}

/**
 * 2016-01-14
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_lc($c) {return df_lcfirst(df_explode_class($c));}

/**
 * 2016-04-11
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * Dfe_CheckoutCom => [dfe, checkout, com]
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_lc_camel($c) {return df_lcfirst(df_explode_class_camel($c));}

/**
 * 2016-01-01
 * «Magento 2 duplicates the «\Interceptor» string constant in 9 places»:
 * https://mage2.pro/t/377
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @used-by dfpm_c()
 * @param string|object $c
 * @return string
 */
function df_interceptor_name($c) {return df_cts($c) . '\Interceptor';}

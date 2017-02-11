<?php
use Df\Core\Exception as DFE;
/**
 * 2016-02-08
 * Применение @uses dfa_flatten() делает возможным вызовы типа:
 * df_cc_class_uc('Aa', ['Bb', 'Cb']) => Aa\Bb\Cb
 * @see df_cc_class_uc()
 * @param string[] ...$args
 * @return string
 */
function df_cc_class(...$args) {return implode('\\', dfa_flatten($args));}

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
 * @used-by \Df\Payment\WebhookF::i()
 * @param string $c
 * @return bool
 */
function df_class_check_abstract($c) {return (new ReflectionClass($c))->isAbstract();}

/**
 * 2017-01-10
 * @uses df_cts() отсекает окончание «\Interceptor»: без этого функция работала бы не так, как мы хотим
 * (возвращала бы путь к файлу из папки «var/generation», а не из папки модуля).
 * Пример результата: «C:/work/mage2.pro/store/vendor/mage2pro/allpay/Webhook/ATM.php».
 * Пока эта функция никем не используется.
 * @param string|object $c
 * @return string
 */
function df_class_file($c) {return df_path_n((new ReflectionClass(df_cts($c)))->getFileName());}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_class_first($c) {return df_first(df_explode_class($c));}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_class_last($c) {return df_last(df_explode_class($c));}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * @param string|object $c
 * @return string
 */
function df_class_last_lc($c) {return df_lcfirst(df_class_last($c));}

/**
 * 2016-01-01
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return bool
 */
function df_class_my($c) {return in_array(df_class_first($c), ['Df', 'Dfe', 'Dfr']);}

/**
 * 2016-07-10
 * Df\PaypalClone\Webhook => Df\PaypalClone\Request
 * @param string|object $c
 * @param string[] $newSuffix
 * @return string
 */
function df_class_replace_last($c, ...$newSuffix) {return
	implode(df_cld($c), array_merge(df_head(df_explode_class($c)), dfa_flatten($newSuffix)))
;}

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
 * @param string|object $c
 * @return string
 */
function df_class_second_lc($c) {return df_lcfirst(df_class_second($c));}

/**
 * 2016-11-25
 * «Df\Sso\Settings\Button» => «Settings\Button»
 * 2017-02-11
 * «Df\Sso\Settings\IButton» => «Settings\Button»
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
 * Функция допускает в качестве $c:
 * 1) Имя класса. Например: «A\B\C».
 * 2) Объект. Сводится к случаю 1 посредством @see get_class()
 * @param string|string[] $suffix
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con($c, $suffix, $def = null, $throw = true) {return
	df_con_generic(function($c, $suffix) {
		/** @var string $del */
		$del = df_cld($c);
		// 2016-11-25
		// Применение df_cc() обязательно, потому что $suffix может быть массивом.
		return df_cc($del, df_module_name($c, $del), $suffix);
	}, $c, $suffix, $def, $throw)
;}

/**
 * Инструмент парадигмы «convention over configuration».
 * 2016-10-26
 * @param \Closure $f
 * @param object|string $c
 * @param string|string[] $suffix
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_generic(\Closure $f, $c, $suffix, $def = null, $throw = true) {return
	dfcf(function($f, $c, $suffix, $def = null, $throw = true) {
		/** @var string $result */
		$result = $f($c, $suffix);
		return df_class_exists($result) ? $result : (
			$def ?: (!$throw ? null : df_error("The «{$result}» class is required."))
		);
	}, [$f, df_cts($c), $suffix, $def, $throw])
;}

/**
 * 2016-10-26
 * @param object|string $c
 * @param string|string[] $suffix
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_child($c, $suffix, $def = null, $throw = true) {return
	df_con_generic(function($c, $suffix) {return
		df_cc(df_cld($c), $c, $suffix)
	;}, $c, $suffix, $def, $throw)
;}

/**
 * 2016-11-25
 * Возвращает имя класса с тем же суффиксом, что и $def,
 * но из папки того же модуля, которому принадлежит класс $c.
 * Результат должен быть наследником класса $def.
 * Если класс не найден, то возвращается $def.
 * Параметр $throw этой функции не нужен, потому что параметр $def обязателен.
 *
 * Пример:
 * $c => \Dfe\FacebookLogin\Button
 * $def = \Df\Sso\Settings\Button
 * Результат: «Dfe\FacebookLogin\Settings\Button»
 *
 * 2016-12-28
 * Отличие от @see df_con_sibling рассмотрим на примере:
 * класс: Dfe\AAA\Webhook\Exception
 * df_con_heir($this, \Df\Payment\Webhook\Report::class)
 * 		ищет сначала \Dfe\AAA\Webhook\Report
 * 		если не найдено — возвращает \Df\Payment\Webhook\Report
 * df_con_sibling($this, 'Webhook\Report', \Df\Payment\Webhook\Report)
 * 		работает точно так же, но запись длиннее
 * 		+ не проверяет, что результат имеет класс \Df\Payment\Webhook\Report или его потомка.
 *
 * @used-by \Df\Sso\Button::s()
 *
 * 2017-02-11
 * Отныне функция позволяет в качестве $def передавать интерфейс: @see df_class_suffix()
 *
 * @param object|string $c
 * @param string $def
 * @return string|null
 */
function df_con_heir($c, $def) {return
	df_ar(df_con(df_module_name_c($c), df_class_suffix($def), $def), $def)
;}

/**
 * 2017-01-04
 * Сначала ищет класс с суффиксом, как у $ar, в папке класса $c,
 * а затем спускается по иерархии наследования для $c,
 * и так до тех пор, пока не найдёт в папке предка класс с суффиксом, как у $ar.
 * 2017-02-11
 * Отныне функция позволяет в качестве $ar передавать интерфейс: @see df_class_suffix()
 * @param object|string $c
 * @param string $ar
 * @return string
 * @throws DFE
 */
function df_con_hier($c, $ar) {
	/** @var string $suffix */
	$suffix = df_class_suffix($ar);
	/** @var string|null $result */
	$result = df_con($c, $suffix, null, false);
	if (!$result) {
		/** @var string|false $c */
		// 2017-01-11
		// Используем df_cts(), чтобы отсечь окончание «\Interceptor».
		$c = get_parent_class(df_cts($c));
		if (!$c) {
			/** @var string $required */
			$required = df_cc_class(df_module_name_c($c), $suffix);
			df_error("The «{$required}» class is required.");
		}
		$result = df_con_hier($c, $ar);
	}
	return df_ar($result, $ar);
}

/**
 * 2016-08-29
 * @used-by dfp_method_call_s()
 * @used-by \Df\StripeClone\Method::chargeNew()
 * @param string|object $c
 * @param string|string[] $suffix
 * @param string $method
 * @param mixed[] $params [optional]
 * @return mixed
 */
function df_con_s($c, $suffix, $method, array $params = []) {return dfcf(
	function($c, $suffix, $method, array $params = []) {
		/** @var string $class */
		$class = df_con($c, $suffix);
		if (!method_exists($class, $method)) {
			df_error('The class %s should define the method «%s».', $class, $method);
		}
		return call_user_func_array([$class, $method], $params);
	}
, func_get_args());}

/**
 * 2016-07-10          
 * 2016-11-25
 * Возвращает имя класса из той же папки, что и $c, но с окончанием $nameLast.
 * Пример:
 * $c => \Df\Payment\Webhook
 * $nameLast = «Exception»
 * Результат: «Df\Payment\Webhook\Exception»
 * @used-by \Df\Payment\Webhook::exceptionC()
 * 2016-12-28
 * Отличие от @see df_con_heir рассмотрим на примере:
 * класс: Dfe\AAA\Webhook\Exception
 * df_con_heir($this, \Df\Payment\Webhook\Report::class)
 * 		ищет сначала \Dfe\AAA\Webhook\Report
 * 		если не найдено — возвращает \Df\Payment\Webhook\Report
 * df_con_sibling($this, 'Webhook\Report', \Df\Payment\Webhook\Report)
 * 		работает точно так же, но запись длиннее
 * 		+ не проверяет, что результат имеет класс \Df\Payment\Webhook\Report или его потомка.
 * 
 * @param object|string $c
 * @param string|string[] $nameLast
 * @param string|null $def [optional]
 * @param bool $throw [optional]
 * @return string|null
 */
function df_con_sibling($c, $nameLast, $def = null, $throw = true) {return
	df_con_generic(function($c, $nameLast) {return
		df_class_replace_last($c, $nameLast)
	;}, $c, $nameLast, $def, $throw)
;}

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
 * @param string|object $c
 * @return string
 */
function df_interceptor_name($c) {return df_cts($c) . '\Interceptor';}

/**
 * «Dfe\AllPay\Webhook» => «Dfe_AllPay»
 *
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * 2016-10-26
 * Функция успешно работает с короткими именами классов:
 * «A\B\C» => «A_B»
 * «A_B» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 *
 * 2017-01-27
 * Так как «A_B» => «A_B», то функция успешно работает с именем модуля:
 * она просто возвращает его без изменений.
 * Таким образом, функция допускает на входе:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 *
 * @used-by df_composer_json()
 * @used-by df_con()     
 * @used-by df_fe_init()
 * @used-by df_module_dir()
 * @used-by df_module_name_c()
 * @used-by df_phtml() 
 * @used-by df_route()
 * @used-by df_x_magento_init()
 * @used-by df_x_magento_init_att()
 * @used-by \Df\Framework\Plugin\View\Element\AbstractBlock::afterGetModuleName()
 * @used-by \Df\Sso\CustomerReturn::execute()
 * @param string|object $c [optional]
 * @param string $del [optional]
 * @return string
 */
function df_module_name($c, $del = '_') {return dfcf(function($c, $del) {return
	implode($del, array_slice(df_explode_class($c), 0, 2))
;}, [df_cts($c), $del]);}

/**
 * 2017-01-04
 * Функция допускает на входе:
 * 1) Имя модуля. Например: «A_B».
 * 2) Имя класса. Например: «A\B\C».
 * 3) Объект. Сводится к случаю 2 посредством @see get_class()
 * @param string|object $c
 * @return string
 */
function df_module_name_c($c) {return df_module_name($c, '\\');}

/**
 * 2016-08-28
 * «Dfe\AllPay\Webhook» => «AllPay»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_module_name_short($c) {return dfcf(function($c) {return
	df_explode_class($c)[1]
;}, [df_cts($c)]);}

/**
 * 2016-02-16
 * «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_module_name_lc($c, $del = '_') {return
	implode($del, df_explode_class_lc_camel(df_module_name_c($c)))
;}
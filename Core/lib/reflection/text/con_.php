<?php
use Df\Core\Exception as DFE;
use Df\Core\R\ConT;

/**
 * 2016-02-08
 * Проверяет наличие следующих классов в указанном порядке:
 * 		1) <имя конечного модуля>\<окончание класса>
 * 		2) $def
 * Возвращает первый из найденных классов.
 * @param object|string $c
 * $c could be:
 * 1) a class name: «A\B\C».
 * 2) an object. It is reduced to case 1 via @see get_class()
 * @used-by dfs_con()
 * @used-by Df\Framework\Mail\TransportObserver::execute()
 * @param string|string[] $suf
 * @return string|null
 */
function df_con($c, $suf, string $def = '', bool $throw = true) {return ConT::generic(
	function($c, $suf) {return
		/** @var string $del */
		# 2016-11-25 Применение df_cc() обязательно, потому что $suf может быть массивом.
		df_cc($del = df_cld($c), df_module_name($c, $del), $suf)
	;}, $c, $suf, $def, $throw
);}

/**
 * 2016-10-26
 * 2022-10-31 @deprecated It is unused.
 * @param object|string $c
 * @param string|string[] $suf
 * @return string|null
 */
function df_con_child($c, $suf, string $def = '', bool $throw = true) {return ConT::generic(
	function($c, $suf) {return df_cc(df_cld($c), $c, $suf);}, $c, $suf, $def, $throw
);}

/**
 * 2016-11-25
 * Возвращает имя класса с тем же суффиксом, что и $def, но из папки того же модуля, которому принадлежит класс $c.
 * Результат должен быть наследником класса $def.
 * Если класс не найден, то возвращается $def.
 * Параметр $throw этой функции не нужен, потому что параметр $def обязателен.
 * Пример:
 * 		$c => \Dfe\FacebookLogin\Button
 * 		$def = \Df\Sso\Settings\Button
 * 		Результат: «Dfe\FacebookLogin\Settings\Button»
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
 * @used-by dfpm_c()
 * @used-by dfsm_c()
 * @used-by Df\API\Facade::p()
 * @used-by Dfe\GingerPaymentsBase\Settings::os()
 * @used-by Df\Payment\Currency::f()
 * @used-by Df\Payment\Facade::s()
 * @used-by Df\PaypalClone\Charge::p()
 * @used-by Df\Sso\Button::s()
 * @used-by Df\Sso\CustomerReturn::c()
 * @used-by Df\StripeClone\Facade\Card::create()
 * @used-by Df\StripeClone\P\Charge::sn()
 * @used-by Df\StripeClone\P\Preorder::request()
 * @used-by Df\StripeClone\P\Reg::request()
 * @used-by Dfe\Zoho\API\Client::i()
 * @param object|string $c
 * @return string|null
 */
function df_con_heir($c, string $def) {return df_ar(df_con(df_module_name_c($c), df_class_suffix($def), $def), $def);}

/**
 * 2017-01-04
 * Сначала ищет класс с суффиксом, как у $ar, в папке класса $c,
 * а затем спускается по иерархии наследования для $c,
 * и так до тех пор, пока не найдёт в папке предка класс с суффиксом, как у $ar.
 * 2017-02-11 Отныне функция позволяет в качестве $ar передавать интерфейс: @see df_class_suffix()
 * @used-by Df\Config\Settings::convention()
 * @used-by Df\Payment\Choice::f()
 * @used-by Df\Payment\Init\Action::sg()
 * @used-by Df\Payment\Method::getFormBlockType()
 * @used-by Df\Payment\Method::getInfoBlockType()
 * @used-by Df\Payment\Method::s()
 * @used-by Df\Payment\Url::f()
 * @used-by Df\Payment\W\F::__construct()
 * @used-by Df\Payment\W\F::s()
 * @used-by Df\Shipping\Method::s()
 * @param object|string $c
 * @return string|null
 * @throws DFE
 */
function df_con_hier($c, string $ar, bool $throw = true) {/** @var string|null $r */ return
	($r = df_con_hier_suf($c, df_class_suffix($ar), $throw)) ? df_ar($r, $ar) : null
;}

/**
 * 2017-03-11
 * @used-by df_con_hier()
 * @param object|string $c
 * @return string|null
 * @throws DFE
 */
function df_con_hier_suf($c, string $suf, bool $throw = true) {/** @var string|null $r */
	if (!($r = df_con($c, $suf, '', false))) {
		# 2017-01-11 Используем df_cts(), чтобы отсечь окончание «\Interceptor».
		if ($parent = get_parent_class(df_cts($c))) {/** @var string|false $parent */
			$r = df_con_hier_suf($parent, $suf, $throw);
		}
		elseif ($throw) {
			/** @var string $expected */
			df_error('df_con_hier_suf(): %s.',
				!df_class_exists($expected = df_cc_class(df_module_name_c($c), $suf))
				? "ascended to the absent class «{$expected}»"
				: (df_is_abstract($expected) ? "ascended to the abstract class «{$expected}»" :
					"unknown error"
				)
			);
		}
	}
	return $r;
}

/**
 * 2017-03-20 Сначала проходит по иерархии суффиксов, и лишь затем — по иерархии наследования.
 * @used-by Df\Payment\W\F::tryTA()
 * @used-by Df\PaypalClone\Signer::_sign()
 * @param object|string $c
 * @param string|string[] $sufBase
 * @param string|string[] $ta
 * @return string|null
 * @throws DFE
 */
function df_con_hier_suf_ta($c, $sufBase, $ta, bool $throw = true) {
	$ta = df_array($ta);
	$sufBase = df_cc_class($sufBase);
	$r = null; /** @var string|null $r */
	$taCopy = $ta; /** @var string[] $taCopy */
	$count = count($ta); /** @var int $count */
	while (-1 < $count && !($r = df_con($c, df_cc_class_uc($sufBase, $ta), '', false))) {
		array_pop($ta); $count--;
	}
	# 2017-01-11 Используем df_cts(), чтобы отсечь окончание «\Interceptor».
	/** @var string|false $parent */
	if (!$r && ($parent = get_parent_class(df_cts($c)))) {
		$r = df_con_hier_suf_ta($parent, $sufBase, $taCopy, $throw);
	}
	return $r || !$throw ? $r : df_error("The «%s» class is required.", df_cc_class_uc(df_module_name_c($c), $sufBase, $ta));
}

/**
 * 2016-08-29
 * @used-by dfpm_call_s()
 * @used-by dfsm_call_s()
 * @used-by Df\StripeClone\Method::chargeNew()
 * @param string|object $c
 * @param string|string[] $suffix
 * @return mixed
 */
function df_con_s($c, $suffix, string $method, array $params = []) {return dfcf(
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
 * 		$c => \Df\Payment\W\Handler
 * 		$nameLast = «Exception»
 * 		Результат: «Df\Payment\W\Handler\Exception»
 * 2016-12-28
 * Отличие от @see df_con_heir рассмотрим на примере:
 * класс: Dfe\AAA\Webhook\Exception
 * df_con_heir($this, \Df\Payment\Xxx\Yyy::class)
 * 		ищет сначала \Dfe\AAA\Xxx\Yyy
 * 		если не найдено — возвращает \Df\Payment\Xxx\Yyy
 * df_con_sibling($this, 'Webhook\Report', \Df\Payment\Xxx\Yyy)
 * 		работает точно так же, но запись длиннее
 * 		+ не проверяет, что результат имеет класс \Df\Payment\Xxx\Yyy или его потомка.
 * @used-by Df\Payment\W\Handler::exceptionC()
 * @param object|string $c
 * @param string|string[] $nameLast
 * @return string|null
 */
function df_con_sibling($c, $nameLast, string $def = '', bool $throw = true) {return ConT::generic(
	function($c, $nameLast) {return df_class_replace_last($c, $nameLast);}, $c, $nameLast, $def, $throw
);}
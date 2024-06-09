<?php
use Df\Core\Exception as DFE;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2016-11-10
 * @used-by df_con_heir()
 * @used-by df_con_hier()
 * @used-by df_eav_update()
 * @used-by df_load()
 * @used-by df_newa()
 * @used-by df_trans()
 * @used-by dfpex_args()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\Operation\Source\Creditmemo::cm()
 * @used-by \Df\Payment\Operation\Source\Order::ii()
 * @used-by \Df\Payment\Operation\Source\Quote::ii()
 * @used-by \Df\Payment\W\Strategy::handle()
 * @used-by \Df\Payment\W\Strategy::m()
 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
 * @param string|object $v
 * @param string|object|null $c [optional]
 * @param string|Th|null $m [optional]
 * @return string|object
 * @throws DFE
 */
function df_ar($v, $c = null, $m = null) {return dfcf(function($v, $c = null, $m = null) {
	if ($c) {
		$c = df_cts($c);
		!is_null($v) ?: df_error($m ?: "Expected class: «{$c}», given `null`.");
		is_object($v) || is_string($v) ?: df_error($m ?: "Expected class: «{$c}», given: %s.", df_type($v));
		$cv = df_assert_class_exists(df_cts($v)); /** @var string $cv */
		if (!is_a($cv, $c, true)) {
			df_error($m ?: "Expected class: «{$c}», given class: «{$cv}».");
		}
	}
	return $v;
}, func_get_args());}

/**
 * 2016-08-03
 * @used-by df_ar()
 * @used-by \Df\Config\Backend\Serialized::entityC()
 * @param string|Th $m [optional]
 * @throws DFE
 */
function df_assert_class_exists(string $c, $m = null):string {
	df_param_sne($c, 0);
	return df_class_exists($c) ? $c : df_error($m ?: "The required class «{$c}» does not exist.");
}

/**
 * 2015-03-04
 * 1) Эта функция проверяет, принадлежит ли переменная $v хотя бы к одному из классов $class.
 * 2) Т.к. алгоритм функции использует стандартный оператор `instanceof`, то переменная $v может быть не только объектом,
 * а иметь произвольный тип: https://php.net/manual/language.operators.type.php#example-146
 * Если $v не является объектом, то функция просто вернёт `false`.
 *
 * Наша функция не загружает при этом $class в память интерпретатора PHP.
 * Если $class ещё не загружен в память интерпретатора PHP, то функция вернёт false.
 * В принципе, это весьма логично!
 * Если проверяемый класс ещё не был загружен в память интерпретатора PHP,
 * то проверяемая переменная $v гарантированно не может принадлежать данному классу!
 * http://3v4l.org/KguI5
 * Наша функция отличается по сфере применения
 * как от оператора instanceof, так и от функции @see is_a() тем, что:
 * 1) Умеет проводить проверку на приналежность не только одному конкретному классу, а и хотя бы одному из нескольких.
 * 2) @is_a() приводит к предупреждению уровня E_DEPRECATED интерпретатора PHP версий ниже 5.3:
 * https://php.net/manual/function.is-a.php
 * 3) Даже при проверке на принадлежность одному классу код с @see df_is() получается короче,
 * чем при применении instanceof в том случае, когда мы не уверены, существует ли класс
 * и загружен ли уже класс интерпретатором PHP.
 * Например, нам приходилось писать так:
 *		class_exists('Df_1C_Cml2Controller', $autoload = false)
 *	&&
 *		df_state()->getController() instanceof Df_1C_Cml2Controller
 * Или так:
 *		$controllerClass = 'Df_1C_Cml2Controller';
 *		$result = df_state()->getController() instanceof $controllerClass;
 * При этом нельзя писать
 *		df_state()->getController() instanceof 'Df_1C_Cml2Controller'
 * потому что правый операнд instanceof может быть строковой переменной, но не может быть просто строкой!
 * https://php.net/manual/language.operators.type.php#example-148
 * 2021-05-31 @deprecated It is unused.
 * 2022-11-26
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @param object|mixed $v
 */
function df_is($v, string ...$cc):bool {return !!df_find($cc, function(string $c) use($v):bool {return $v instanceof $c;});}
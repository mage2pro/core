<?php
use Df\Core\RAM;
/**
 * 2016-08-31
 * Кэш должен быть не глобальным, а храниться внутри самого объекта по 2 причинам:
 * 1) @see spl_object_hash() может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * https://php.net/manual/function.spl-object-hash.php#76220
 * 2) после уничтожения объекта нефиг замусоривать память его кэшем.
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 * 2017-01-01
 * 1) Мы не можем кэшировать Closure самодостаточно, в отрыве от объекта,
 * потому что Closure может обращаться к объекту через $this (свойства, методы).
 * 2) При $unique = false Closure $f будет участвовать в расчёте ключа кэширования.
 * Это нужно в 2 ситуациях:
 * 2.1) Если Ваш метод содержит несколько вызовов dfc() для разных Closure.
 * 2.2) В случаях, подобных @see dfaoc(), когда Closure передаётся в метод в качестве параметра,
 * и поэтому Closure не уникальна.
 * 2017-01-02 Задавайте параметр $offset в том случае, когда dfc() вызывается опосредованно. Например, так делает @see dfaoc().
 * 2022-11-17
 * 1) `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) Previously, I had a df_once() function which only difference from dfc() was a void result of $f.
 * Today I have noticed that you can use a void-result $f with dfc(): https://3v4l.org/CYJ1X
 * So I removed df_once().
 * 3) The methods which use dfc() with a void-result $f:
 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
 * @used-by \Df\Sso\Button::_prepareLayout()
 * ---
 * @used-by dfaoc()
 * @see df_prop()
 * @see dfaoc()
 * @param object $o
 * @return mixed
 */
function dfc($o, Closure $f, array $a = [], bool $unique = true, int $offset = 0) {
	/**
	 * 2021-10-05
	 * I do not use @see df_bt() to make the implementation faster. An implementation via df_bt() is:
	 * 		$b = df_bt(0, 2 + $offset)[1 + $offset];
	 */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset]; /** @var array(string => string) $b */
	if (!isset($b['class'], $b['function'])) {
		df_error("[dfc] Invalid backtrace frame:\n" . df_dump($b)); # 2017-01-02 Usually it means that $offset is wrong.
	}
	/** @var string $k */ /** 2022-11-17 @see df_cc_method() */
	$k = "{$b['class']}::{$b['function']}" . (!$a ? null : df_hash_a($a)) . ($unique ? null : spl_object_hash($f));
	/**
	 * 2022-10-17
	 * 1) Dynamic properties are deprecated since PHP 8.2:
	 * https://php.net/manual/migration82.deprecated.php#migration82.deprecated.core.dynamic-properties
	 * https://wiki.php.net/rfc/deprecate_dynamic_properties
	 * 2) @see df_prop_k()
	 * @var mixed $r
	 */
	static $hasWeakMap; /** @var bool $hasWeakMap */
	if (!($hasWeakMap = !is_null($hasWeakMap) ? $hasWeakMap : @class_exists('WeakMap'))) {
		# 2017-01-12 ... works correctly here: https://3v4l.org/0shto
		# 2022-10-17 The ternary operator works correctly here: https://3v4l.org/MutM4
		/** @noinspection PhpVariableVariableInspection */
		$r = property_exists($o, $k) ? $o->$k : $o->$k = $f(...$a);
	}
	else {
		static $map; /** @var WeakMap $map */
		$map = $map ?: new WeakMap;
		if (!$map->offsetExists($o)) {
			$map[$o] = [];
		}
		# 2022-10-17 https://3v4l.org/6cVAu
		$map2 =& $map[$o]; /** @var array(string => mixed) $map2 */
		/**
		 * 2017-01-12 ... works correctly here: https://3v4l.org/0shto
		 * 2022-10-17 The ternary operator works correctly here: https://3v4l.org/MutM4
		 * 2022-10-27 We can not use @see isset() here: https://3v4l.org/FhAUv
		 * 2022-10-28 @see \Df\Core\RAM::exists()
		 */
		$r = array_key_exists($k, $map2) ? $map2[$k] : $map2[$k] = $f(...$a);
	}
	return $r;
}

/**
 * 2016-09-04
 * Не используем решения типа такого: http://stackoverflow.com/a/34711505
 * потому что они возвращают @see Closure, и тогда кэшируемая функция становится переменной,
 * что неудобно (неунифицировано и засоряет глобальную область видимости переменными).
 * @param Closure $f
 * Используем именно array $a = [], а не ...$a,
 * чтобы кэшируемая функция не перечисляла свои аргументы при передачи их сюда,
 * а просто вызывала @see func_get_args()
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 * 2017-01-01
 * Мы не можем кэшировать Closure самодостаточно, в отрыве от класса,
 * потому что Closure может обращаться к полям и методам класса через self и static.
 * 2017-01-01
 * При $unique = false Closure $f будет участвовать в расчёте ключа кэширования.
 * Это нужно в 2 ситуациях:
 * 1) Если Ваша функция содержит несколько вызовов dfc() для разных Closure.
 * 2) В случаях, подобных @see dfac(), когда Closure передаётся в функцию в качестве параметра,
 * и поэтому Closure не уникальна.
 * 2017-08-11 The cache tags. A usage example: @see df_cache_get_simple()
 * 2017-01-02 Задавайте параметр $offset в том случае, когда dfc() вызывается опосредованно. Например, так делает @see dfac().
 * @see dfac()
 * @used-by df_category_children_map()
 * @used-by df_currency()
 * @used-by df_google_init_service_account()
 * @used-by df_module_file_read()
 * @used-by df_modules_my()
 * @used-by df_mvars()
 * @used-by df_product_images_path_rel()
 * @used-by dfac()
 * @used-by \Df\Core\Session::s()
 * @used-by \Df\Core\Text\Regex::getErrorCodeMap()
 * @used-by \Df\OAuth\App::state()
 * @used-by \Df\Payment\Url::f()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @return mixed
 */
function dfcf(Closure $f, array $a = [], array $tags = [], bool $unique = true, int $offset = 0) {
	/**
	 * 2021-10-05
	 * I do not use @see df_bt() to make the implementation faster. An implementation via df_bt() is:
	 * 		$b = df_bt(0, 2 + $offset)[1 + $offset];
	 */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset]; /** @var array(string => string) $b */
	/**
	 * 2016-09-04
	 * Когда мы кэшируем статический метод, то ключ «class» присутствует,
	 * а когда функцию — то отсутствует: https://3v4l.org/ehu4O
	 * Ради ускорения не используем свои функции dfa() и df_cc().
	 * 2016-11-24
	 * Когда мы кэшируем статический метод, то значением ключа «class» является не вызванный класс,
	 * а тот класс, где определён кэшируемый метод: https://3v4l.org/OM5sD
	 * Поэтому все потомки класса с кэшированным методом будут разделять общий кэш.
	 * Поэтому если Вы хотите, чтобы потомки имели индивидуальный кэш,
	 * то учитывайте это при вызове dfcf.
	 * Например, пишите не так:
	 *		private static function sModule() {return dfcf(function() {return
	 *			S::convention(static::class)
	 *		;});}
	 * а так:
	 *		private static function sModule() {return dfcf(function($c) {return
	 *			S::convention($c)
	 *		;}, [static::class]);}
	 *
	 * У нас нет возможности вычислять имя вызвавшего нас класса автоматически:
	 * как уже было сказано выше, debug_backtrace() возвращает только имя класса, где метод был объявлен,
	 * а не вызванного класса.
	 * А get_called_class() мы здесь не можем вызывать вовсе:
	 * «Warning: get_called_class() called from outside a class»
	 * https://3v4l.org/ioT7c
	 * 2022-11-17 @see df_cc_method()
	 */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function']
		. (!$a ? null : '--' . df_hash_a($a))
		. ($unique ? null : '--' . spl_object_hash($f))
	; /** @var string $k */
	$r = df_ram(); /** @var RAM $r */
	# 2017-01-12
	# The following code will return `3`:
	# 		$a = function($a, $b) {return $a + $b;};
	# 		$b = [1, 2];
	# 		echo $a(...$b);
	# https://3v4l.org/0shto
	return $r->exists($k) ? $r->get($k) : $r->set($k, $f(...$a), $tags);
}
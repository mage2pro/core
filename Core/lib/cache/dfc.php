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
 * @see df_no_rec()
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
	# 2024-01-10
	# 1) The previous code: `@class_exists('WeakMap')`.
	# 2) I changed the code by analogy with
	# https://github.com/thehcginstitute-com/m1/blob/2024-01-10-2/app/code/local/Df/Core/lib/cache/dfc.php#L58-L62
	if (!($hasWeakMap = !is_null($hasWeakMap) ? $hasWeakMap : class_exists('WeakMap', false))) {
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
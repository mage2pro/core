<?php
use Magento\Framework\App;
/**
 * 2015-08-13
 * @return App\CacheInterface|App\Cache
 */
function df_cache() {return df_o(App\CacheInterface::class);}

/**
 * 2015-08-13
 * https://mage2.pro/t/52
 * @param string $type
 * @return bool
 */
function df_cache_enabled($type) {
	/** @var App\Cache\StateInterface|App\Cache\State $cacheState */
	$cacheState = df_o(App\Cache\StateInterface::class);
	return $cacheState->isEnabled($type);
}

/**
 * 2016-07-18
 * 2016-10-28
 * Добавил дополнительный уровень кэширования: в оперативной памяти.
 * Также позволил в качестве $key передавать массив.
 *
 * 2016-11-01
 * При вызове @see df_cache_get_simple синтаксис use для параметра $f использовать безопасно,
 * в отличие от @see dfc() и @see dfcf(), потому что ключ кэширования передаётся параметром $key.
 *
 * @param string|string[] $key
 * @param callable $f
 * @param mixed[] ...$arguments [optional]
 * @return mixed
 */
function df_cache_get_simple($key, callable $f, ...$arguments) {return
	// 2016-11-01
	// Осознанно передаём параметры $method и $arguments через use,
	// потому что нам не нужно учитывать их в расчёте ключа кэша,
	// ведь $key — уже готовый ключ.
	dfcf(function($key) use ($f, $arguments) {
		/** @var string|bool $resultS */
		$resultS = df_cache_load($key);
		/** @var mixed $result */
		$result = null;
		if (false !== $resultS) {
			/** @var array(string => mixed) $result */
			$result = df_unserialize_simple($resultS);
		}
		/**
		 * 2016-10-28
		 * json_encode(null) возвращает строку 'null',
		 * а json_decode('null') возвращает null.
		 * Поэтому если $resultS равно строке 'null',
		 * то нам не надо вызывать функцию: она уже вызывалась,
		 * и (кэшированным) результатом этого вызова было значение null.
		 */
		if (null === $result && 'null' !== $resultS) {
			$result = call_user_func_array($f, $arguments);
			df_cache_save(df_serialize_simple($result), $key);
		}
		return $result;
	}, [!is_array($key) ? $key : dfa_hashm($key)])
;}

/**
 * 2016-08-25
 * Можно, конечно, реализовать функцию как return df_cc('::', $params);
 * но для ускорения я сделал иначе.
 * @param string[] ...$p
 * @return string
 */
function df_ckey(...$p) {return !$p ? '' : implode('::', is_array($p[0]) ? $p[0] : $p);}

/**
 * 2015-08-13
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}

/**
 * 2016-07-18
 * @param mixed $data
 * @param string $key
 * @param string[] $tags [optional]
 * @param int|null $lifeTime [optional]
 * @return bool
 */
function df_cache_save($data, $key, $tags = [], $lifeTime = null) {return
	df_cache()->save($data, $key, $tags, $lifeTime)
;}

/**
 * 2016-08-31
 * Кэш должен быть не глобальным, а храниться внутри самого объекта по 2 причинам:
 * 1) @see spl_object_hash() может вернуть одно и то же значение для разных объектов,
 * если первый объект уже был уничтожен на момент повторного вызова spl_object_hash():
 * http://php.net/manual/en/function.spl-object-hash.php#76220
 * 2) после уничтожения объекта нефиг замусоривать память его кэшем.
 *
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 *
 * @param object $o
 * @param \Closure $m
 * @param mixed[] $a [optional]
 * @return mixed
 */
function dfc($o, \Closure $m, array $a = []) {
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	/** @var string $k */
	$k = $b['class'] . '::' . $b['function'] . (!$a ? null : dfa_hash($a));
	return property_exists($o, $k) ? $o->$k : $o->$k = call_user_func_array($m, $a);
}

/**
 * 2016-09-04
 * Не используем решения типа такого: http://stackoverflow.com/a/34711505
 * потому что они возвращают @see \Closure, и тогда кэшируемая функция становится переменной,
 * что неудобно (неунифицировано и засоряет глобальную область видимости переменными).
 * @param \Closure $f
 * Используем именно  array $a = [], а не ...$a,
 * чтобы кэшируемая функция не перечисляла свои аргументы при передачи их сюда,
 * а просто вызывала @see func_get_args()
 *
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 *
 * @param mixed[] $a [optional]
 * @return mixed
 */
function dfcf(\Closure $f, array $a = []) {
	/** @var array(string => mixed) $c */
	static $c = [];
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	/**
	 * 2016-09-04
	 * Когда мы кэшируем статический метод, то ключ «class» присутствует,
	 * а когда функцию — то отсутствует: https://3v4l.org/ehu4O
	 * Ради ускорения не используем свои функции dfa() и df_cc().
	 *
	 * 2016-11-24
	 * Когда мы кэшируем статический метод, то значением ключа «class» является не вызванный класс,
	 * а тот класс, где определён кэшируемый метод: https://3v4l.org/OM5sD
	 * Поэтому все потомки класса с кэшированным методом будут разделять общий кэш.
	 * Поэтому если Вы хотите, чтобы потомки имели индивидуальный кэш,
	 * то учитывайте это при вызове dfcf.
	 * Например, пишите не так:
			private static function sModule() {return dfcf(function() {return
				S::convention(static::class)
			;});}
	 * а так:
			private static function sModule() {return dfcf(function($c) {return
				S::convention($c)
			;}, [static::class]);}
	 *
	 * У нас нет возможности вычислять имя вызвавшего нас класса автоматически:
	 * как уже было сказано выше, debug_backtrace() возвращает только имя класса, где метод был объявлен,
	 * а не вызванного класса.
	 * А get_called_class() мы здесь не можем вызывать вовсе:
	 * «Warning: get_called_class() called from outside a class»
	 * https://3v4l.org/ioT7c
	 */
	/** @var string $k */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function'] . (!$a ? null : dfa_hash($a));
	// 2016-09-04
	// https://3v4l.org/9cQOO
	return array_key_exists($k, $c) ? $c[$k] : $c[$k] = call_user_func_array($f, $a);
}
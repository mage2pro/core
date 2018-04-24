<?php
use Df\Core\RAM;
use Magento\Framework\App\Cache;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Cache\StateInterface as IState;
use Magento\Framework\App\Cache\TypeList;
use Magento\Framework\App\Cache\TypeListInterface as ITypeList;
use Magento\Framework\App\CacheInterface as ICache;
use Magento\Framework\Cache\FrontendInterface as IFrontend;
// 2017-08-10
// Previsously, we had \Df\Core\Cache class: https://github.com/mage2pro/core/blob/2.10.12/Core/Cache.php

/**
 * 2015-08-13
 * @return ICache|Cache
 */
function df_cache() {return df_o(ICache::class);}

/**
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
 * @used-by \Dfe\Moip\Backend\Enable::dfSaveAfter()
 */
function df_cache_clean() {
	df_map(function(IFrontend $f) {$f->getBackend()->clean();}, df_cache_pool());
	df_ram()->reset();
	/**
	 * 2017-10-19
	 * It is important, because M2 caches the configuration values in RAM:
	 * @see \Magento\Config\App\Config\Type\System::get()
	 */
	df_cfg_m()->clean();
}

/**
 * 2017-08-11
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @uses \Magento\Framework\App\Cache\TypeList::cleanType()
 * @used-by \Df\API\Client::p()
 * @param string $tag
 */
function df_cache_clean_tag($tag) {
	df_cache()->clean([$tag]);
	df_ram()->clean($tag);
}

/**
 * 2017-08-11 For now it is never used.
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @uses \Magento\Framework\App\Cache\TypeList::cleanType()
 * @param string[] ...$types
 */
function df_cache_clean_types(...$types) {array_map(
	[df_cache_type_list(), 'cleanType'], dfa_flatten(func_get_args())
);;}

/**
 * 2015-08-13
 * https://mage2.pro/t/52
 * @param string $type
 * @return bool
 */
function df_cache_enabled($type) {
	$state = df_o(IState::class); /** @var IState|State $state */
	return $state->isEnabled($type);
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
 * @used-by \Df\API\Client::p()
 * @used-by \Df\GingerPaymentsBase\Api::idealBanks()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by dfe_portal_stripe_customers()
 * @param string|string[]|null $k
 * @param callable $f
 * @param string[] $tags [optional]
 * @param mixed[] ...$args [optional]
 * @return mixed
 */
function df_cache_get_simple($k, callable $f, $tags = [], ...$args) {return
	// 2016-11-01
	// Осознанно передаём параметры $f и $args через use,
	// потому что нам не нужно учитывать их в расчёте ключа кэша, ведь $k — уже готовый ключ.
	dfcf(function($k) use($f, $tags, $args) {
		$result = null; /** @var mixed $result */
		if (false !== ($resultS = df_cache_load($k))) { /** @var string|bool $resultS */
			$result = df_unserialize_simple($resultS); /** @var array(string => mixed) $result */
		}
		// 2016-10-28
		// json_encode(null) возвращает строку 'null', а json_decode('null') возвращает null.
		// Поэтому если $resultS равно строке 'null', то нам не надо вызывать функцию:
		// она уже вызывалась, и (кэшированным) результатом этого вызова было значение null.
		if (null === $result && 'null' !== $resultS) {
			df_cache_save(df_serialize_simple($result = call_user_func_array($f, $args)), $k, $tags);
		}
		return $result;
		/**
		 * 2017-08-11 We use md5() to make the cache key valid as a file name (for a filesystem-based caching).
		 * 2018-04-24
		 * Previously I have used `df_caller_mm(3)` here.
		 * Maybe the @uses dfa_hash() and @uses md5() were included in the backtrace
		 * in previous PHP versions (e.g. PHP 5.6)?
		 * dfa_hash() and md5() are not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
		 * (I have checked it in the both XDebug enabled and disabled cases).
		 */
	}, [md5(!$k ? dfa_hash([df_caller_mm(1), $args]) : (is_array($k) ? dfa_hash($k) : $k))], $tags)
;}

/**
 * 2015-08-13
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}

/**
 * 2017-06-30
 * @used-by df_cache_clean()
 * @return Pool
 */
function df_cache_pool() {return df_o(Pool::class);}

/**
 * 2016-07-18
 * 2017-02-01
 * It will cache forever if $lifeTime is null: https://mage2.pro/t/2584
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @param mixed $data
 * @param string $key
 * @param string[] $tags [optional]
 * @param int|null $lifeTime [optional]
 * @return bool
 */
function df_cache_save($data, $key, $tags = [], $lifeTime = null) {return df_cache()->save(
	$data, $key, $tags, $lifeTime
);}

/**
 * 2017-06-30
 * @used-by df_cache_clean()
 * @return ITypeList|TypeList
 */
function df_cache_type_list() {return df_o(ITypeList::class);}

/**
 * 2017-08-28
 * @used-by df_cache_clean()
 * @used-by df_cache_clean_tag()
 * @used-by dfcf()
 * @used-by \Df\Payment\Method::sgReset()
 * @return RAM
 */
function df_ram() {return RAM::s();}

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
 * 2017-01-01
 * Мы не можем кэшировать Closure самодостаточно, в отрыве от объекта,
 * потому что Closure может обращаться к объекту через $this (свойства, методы).
 *
 * @param object $o
 * @param \Closure $m
 * @param mixed[] $a [optional]
 * 2017-01-01
 * При $unique = false Closure $m будет участвовать в расчёте ключа кэширования.
 * Это нужно в 2 ситуациях:
 * 1) Если Ваш метод содержит несколько вызовов dfc() для разных Closure.
 * 2) В случаях, подобных @see dfak(), когда Closure передаётся в метод в качестве параметра,
 * и поэтому Closure не уникальна.
 * @param bool $unique [optional]
 * 2017-01-02
 * Задавайте этот параметр в том случае, когда dfc() вызывается опосредованно.
 * Например, так делает @see dfak().
 * @param int $offset [optional]
 * @return mixed
 */
function dfc($o, \Closure $m, array $a = [], $unique = true, $offset = 0) {
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset];
	if (!isset($b['class'], $b['function'])) {
		// 2017-01-02 Обычно этот сбой означает, что нужно задать верное значение параметра $offset.
		df_error("Invalid backtrace frame:\n" . df_dump($b));
	}
	$k = $b['class'] . '::' . $b['function']
		 . (!$a ? null : dfa_hash($a))
		 . ($unique ? null : spl_object_hash($m))
	; /** @var string $k */
	// 2017-01-12 https://3v4l.org/0shto
	return property_exists($o, $k) ? $o->$k : $o->$k = $m(...$a);
}

/**
 * 2016-09-04
 * Не используем решения типа такого: http://stackoverflow.com/a/34711505
 * потому что они возвращают @see \Closure, и тогда кэшируемая функция становится переменной,
 * что неудобно (неунифицировано и засоряет глобальную область видимости переменными).
 * @param \Closure $f
 * Используем именно array $a = [], а не ...$a,
 * чтобы кэшируемая функция не перечисляла свои аргументы при передачи их сюда,
 * а просто вызывала @see func_get_args()
 *
 * 2016-11-01
 * Будьте осторожны при передаче в функцию $f параметров посредством use:
 * эти параметры не будут участвовать в расчёте ключа кэша.
 *
 * 2017-01-01
 * Мы не можем кэшировать Closure самодостаточно, в отрыве от класса,
 * потому что Closure может обращаться к полям и методам класса через self и static.
 *
 * @param mixed[] $a [optional]
 * 2017-01-01
 * При $unique = false Closure $m будет участвовать в расчёте ключа кэширования.
 * Это нужно в 2 ситуациях:
 * 1) Если Ваша функция содержит несколько вызовов dfc() для разных Closure.
 * 2) В случаях, подобных @see dfak(), когда Closure передаётся в функцию в качестве параметра,
 * и поэтому Closure не уникальна.
 *
 * 2017-08-11 The cache tags. A usage example: @see df_cache_get_simple()
 * @param string[] $tags [optional]
 *
 * @param bool $unique [optional]
 * 2017-01-02
 * Задавайте этот параметр в том случае, когда dfc() вызывается опосредованно.
 * Например, так делает @see dfak().
 * @param int $offset [optional]
 *
 * 2017-08-10
 * The usages with 5 arguments:
 * @used-by dfak()
 * @used-by \Df\OAuth\App::state()
 * @used-by \Df\Payment\Url::f()
 *
 * @return mixed
 */
function dfcf(\Closure $f, array $a = [], array $tags = [], $unique = true, $offset = 0) {
	/** @var array(string => string) $b */
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset];
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
	 */
	/** @var string $k */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function']
		. (!$a ? null : '--' . dfa_hash($a))
		. ($unique ? null : '--' . spl_object_hash($f))
	;
	$r = df_ram(); /** @var RAM $r */
	/**
	 * 2017-01-12
	 * The following code will return `3`:
	 * 		$a = function($a, $b) {return $a + $b;};
	 * 		$b = [1, 2];
	 * 		echo $a(...$b);
	 * https://3v4l.org/0shto
	 */
	return $r->exists($k) ? $r->get($k) : $r->set($k, $f(...$a), $tags);
}
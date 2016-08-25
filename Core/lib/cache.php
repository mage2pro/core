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
 * @param string $key
 * @param callable|string $method
 * @param mixed[] ...$arguments [optional]
 * @return mixed
 */
function df_cache_get_simple($key, $method, ...$arguments) {
	/** @var string|bool $resultS */
	$resultS = df_cache_load($key);
	/** @var mixed $result */
	$result = null;
	if (false !== $resultS) {
		/** @var array(string => mixed) $result */
		$result = df_unserialize_simple($resultS);
	}
	if (null === $result) {
		$result = call_user_func_array($method, $arguments);
		df_cache_save(df_serialize_simple($result), $key);
	}
	return $result;
}

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
function df_cache_save($data, $key, $tags = [], $lifeTime = null) {
	return df_cache()->save($data, $key, $tags, $lifeTime);
}
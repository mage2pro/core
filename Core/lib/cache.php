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
 * 2015-08-13
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}



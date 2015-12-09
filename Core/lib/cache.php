<?php
/**
 * 2015-08-13
 * @return \Magento\Framework\App\CacheInterface|\Magento\Framework\App\Cache
 */
function df_cache() {return df_o('Magento\Framework\App\CacheInterface');}

/**
 * 2015-08-13
 * https://mage2.pro/t/52
 * @param string $type
 * @return bool
 */
function df_cache_enabled($type) {
	/** @var \Magento\Framework\App\Cache\StateInterface|\Magento\Framework\App\Cache\State $cacheState */
	$cacheState = df_o('Magento\Framework\App\Cache\StateInterface');
	return $cacheState->isEnabled($type);
}

/**
 * 2015-08-13
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}



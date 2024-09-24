<?php
use Df\Core\RAM;
use Magento\Framework\App\Cache;
use Magento\Framework\App\CacheInterface as ICache;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeList;
use Magento\Framework\App\Cache\TypeListInterface as ITypeList;
/**
 * 2015-08-13
 * @used-by df_cache_clean_tag()
 * @used-by df_cache_load()
 * @used-by df_cache_save()
 * @return Cache|ICache
 */
function df_cache() {return df_o(ICache::class);}

/**
 * 2017-06-30
 * @used-by df_cache_clean()
 */
function df_cache_pool():Pool {return df_o(Pool::class);}

/**
 * 2017-06-30
 * @used-by df_cache_clean_type()
 * @return ITypeList|TypeList
 */
function df_cache_type_list() {return df_o(ITypeList::class);}

/**
 * 2017-08-28
 * @used-by df_cache_clean()
 * @used-by df_cache_clean_tag()
 * @used-by dfcf()
 * @used-by Df\Payment\Method::sgReset()
 */
function df_ram():RAM {return RAM::s();}
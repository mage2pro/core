<?php
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Cache\StateInterface as IState;
# 2017-08-10 Previsously, I had \Df\Core\Cache class: https://github.com/mage2pro/core/blob/2.10.12/Core/Cache.php

/**
 * 2015-08-13 https://mage2.pro/t/52
 * 2021-03-06 @deprecated It is unused.
 * @param string $type
 */
function df_cache_enabled($type):bool {
	$state = df_o(IState::class); /** @var IState|State $state */
	return $state->isEnabled($type);
}

/**
 * 2016-07-18
 * 2016-10-28
 * Добавил дополнительный уровень кэширования: в оперативной памяти.
 * Также позволил в качестве $key передавать массив.
 * 2016-11-01
 * При вызове @see df_cache_get_simple синтаксис use для параметра $f использовать безопасно,
 * в отличие от @see dfc() и @see dfcf(), потому что ключ кэширования передаётся параметром $key.
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Df\API\Client::p()
 * @used-by \Df\GingerPaymentsBase\Api::idealBanks()
 * @used-by \Df\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 * @param string|string[]|null $k
 * @param string[] $tags [optional]
 * @param mixed ...$args [optional]
 * @return mixed
 */
function df_cache_get_simple($k, callable $f, $tags = [], ...$args) {return
	# 2016-11-01
	# Осознанно передаём параметры $f и $args через use,
	# потому что нам не нужно учитывать их в расчёте ключа кэша, ведь $k — уже готовый ключ.
	dfcf(function($k) use($f, $tags, $args) {
		$r = null; /** @var mixed $r */
		if (false !== ($resultS = df_cache_load($k))) { /** @var string|bool $resultS */
			$r = df_json_decode($resultS); /** @var array(string => mixed) $result */
		}
		# 2016-10-28
		# json_encode(null) возвращает строку 'null', а json_decode('null') возвращает null.
		# Поэтому если $resultS равно строке 'null', то нам не надо вызывать функцию:
		# она уже вызывалась, и (кэшированным) результатом этого вызова было значение null.
		if (null === $r && 'null' !== $resultS) {
			df_cache_save(json_encode($r = call_user_func_array($f, $args)), $k, $tags);
		}
		return $r;
		/**
		 * 2017-08-11 We use md5() to make the cache key valid as a file name (for a filesystem-based caching).
		 * 2018-04-24
		 * Previously I have used `df_caller_mm(3)` here.
		 * Maybe the @uses df_hash_a() and @uses md5() were included in the backtrace
		 * in previous PHP versions (e.g. PHP 5.6)?
		 * df_hash_a() and md5() are not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
		 * (I have checked it in the both XDebug enabled and disabled cases).
		 * 2019-01-23 The previous code was: df_caller_mm(1).
		 */
	}, [md5(!$k ? df_hash_a([df_caller_m(), $args]) : (is_array($k) ? df_hash_a($k) : $k))], $tags)
;}

/**
 * 2015-08-13
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\Color\Image::dominant()
 * @param string $key
 * @return string|false
 */
function df_cache_load($key) {return df_cache()->load($key);}

/**
 * 2016-07-18
 * 2017-02-01 It will cache forever if $lifeTime is null: https://mage2.pro/t/2584
 * @used-by df_cache_get_simple()
 * @used-by \Df\Sales\Observer\OrderPlaceAfter::execute()
 * @used-by \Dfe\Color\Image::dominant()
 * @param mixed $data
 * @param string $key
 * @param string[] $tags [optional]
 * @param int|null $lifeTime [optional]
 */
function df_cache_save($data, $key, $tags = [], $lifeTime = null):bool {return df_cache()->save(
	$data, $key, $tags, $lifeTime
);}
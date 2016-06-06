<?php
use Magento\Framework\App\Config\ScopePool;
use Magento\Framework\App\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use Magento\Store\Model\Store;
/**
 * 2015-12-26
 * https://mage2.pro/t/359
 * «Propose to make the @see \Magento\Framework\App\Config\ScopePool::_getScopeCode() public
 * because it is useful to calculate cache keys based on a scope
 * (like @see \Magento\Framework\App\Config\ScopePool::getScope() does)».
 *
 * @param null|string|int|ScopeInterface $scope [optional]
 * @param string|null $scopeType [optional]
 * @param ScopePool $scopePool [optional]
 * @return string
 */
function df_scope_code(
	$scope = null, $scopeType = StoreScopeInterface::SCOPE_STORE, ScopePool $scopePool = null
) {
	return \Df\Framework\App\Config\ScopePool::code($scope, $scopeType, $scopePool);
}

/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return StoreInterface|Store
 * @throws \Magento\Framework\Exception\NoSuchEntityException|Exception
 * https://github.com/magento/magento2/issues/2222
 */
function df_store($store = null) {
	/** @var StoreInterface $result */
	$result = $store;
	if (is_null($result)) {
		/**
		 * 2015-11-04
		 * По аналогии с @see \Magento\Store\Model\StoreResolver::getCurrentStoreId()
		 * https://github.com/magento/magento2/blob/f578e54e093c31378ca981cfe336f7e651194585/app/code/Magento/Store/Model/StoreResolver.php#L82
		 */
		/** @var string|null $storeCode */
		$storeCode = df_request(\Magento\Store\Model\StoreResolver::PARAM_NAME);
		if (is_null($storeCode)) {
			$storeCode = df_store_cookie_m()->getStoreCodeFromCookie();
		}
		if (is_null($storeCode)) {
			$storeCode = df_request('store-view');
		}
		if (is_null($storeCode)) {
			/** @var string $storeCodeFromDfUrl */
			static $storeCodeFromDfUrl;
			if (!isset($storeCodeFromDfUrl)) {
				$storeCodeFromDfUrl = df_n_set(df_preg_match(
					'#/store\-view/([^/]+)/#u', df_ruri(), $needThrow = false
				));
			}
			$storeCode = df_n_get($storeCodeFromDfUrl);
		}
		/**
		 * 2015-08-10
		 * Доработал алгоритм.
		 * Сначала мы смотрим, не находимся ли мы в административной части,
		 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
		 * По аналогии с @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
		 *
		 * 2015-09-20
		 * При единственном магазине
		 * вызываемый ниже метод метод @uses \Df\Core\State::getStoreProcessed()
		 * возвратит витрину default, однако при нахождении в административной части
		 * нам нужно вернуть витрину «admin».
		 * Например, это нужно, чтобы правильно работала функция @used-by df_is_backend()
		 * Переменная $coreCurrentStore в данной точке содержит витрину «admin».
		 *
		 * 2015-11-04
		 * При нахождении в административном интерфейсе
		 * и при отсутствии в веб-адресе идентификатора магазина
		 * этот метод вернёт витрину по по-умолчанию, а не витрину «admin».
		 *
		 * Не знаю, правильно ли это, то так делает этот метод в Российской сборке для Magento 1.x,
		 * поэтому решил пока не менять поведение.
		 *
		 * В Magento 2 же стандартный метод \Magento\Store\Model\StoreManager::getStore()
		 * при вызове без параметров возвращает именно витрину по умолчанию, а не витрину «admin»:
		 * https://github.com/magento/magento2/issues/2254
		 * «The call for \Magento\Store\Model\StoreManager::getStore() without parameters
		 * inside the backend returns the default frontend store, not the «admin» store,
		 * which is inconsistent with Magento 1.x behaviour and I think it will lead to developer mistakes.»
		 */
		if (is_null($storeCode) && df_is_backend()) {
			$storeCode = df_request('store', 'admin');
		}
		if (!is_null($storeCode)) {
			$result = df_store_m()->getStore($storeCode);
		}
	}
	return is_object($result) ? $result : df_store_m()->getStore($result);
}

/**
 * 2016-01-30
 * @param null|string|int|ScopeInterface $store [optional]
 * @return string
 */
function df_store_code($store = null) {return df_scope_code($store);}

/**
 * 2016-01-11
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return int
 */
function df_store_id($store = null) {return df_store($store)->getId();}

/**
 * 2016-01-11
 * @param bool $withDefault [optional]
 * @return int[]
 */
function df_store_ids($withDefault = false) {return array_keys(df_stores($withDefault));}

/**
 * @return \Magento\Store\Model\StoreManagerInterface|\Magento\Store\Model\StoreManager
 */
function df_store_m() {
	static $r; return $r ? $r : $r = df_o(\Magento\Store\Model\StoreManagerInterface::class);
}

/**
 * 2016-01-11
 * @param bool $withDefault [optional]
 * @param bool $codeKey [optional]
 * @return string[]
 */
function df_store_names($withDefault = false, $codeKey = false) {
	return array_map(function(StoreInterface $store) {
		/** @var Store $store */
		return $store->getName();
	}, df_stores($withDefault, $codeKey));
}

/**
 * 2016-01-11
 * @param bool $withDefault [optional]
 * @param bool $codeKey [optional]
 * @return array|\Magento\Store\Api\Data\StoreInterface[]
 */
function df_stores($withDefault = false, $codeKey = false) {
	/**
	 * 2016-01-29
	 * Добави @uses df_ksort(), потому что иначе порядок элементов различается
	 * в зависимости от того, загружается ли страница из кэша или нет.
	 * Для модуля Dfe\SalesSequence нам нужен фиксированный порядок.
	 */
	return df_ksort(df_store_m()->getStores($withDefault, $codeKey));
}


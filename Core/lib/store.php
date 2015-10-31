<?php
/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 * @param int|string|null|bool|\Magento\Store\Api\Data\StoreInterface $store [optional]
 * @return \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store
 * @throws \Magento\Framework\Exception\NoSuchEntityException|Exception
 * https://github.com/magento/magento2/issues/2222
 */
function rm_store($store = null) {
	/** @var \Magento\Store\Api\Data\StoreInterface $result */
	$result = $store;
	if (is_null($result)) {
		/**
		 * 2015-08-10
		 * Доработал алгоритм.
		 * Сначала мы смотрим, не находимся ли мы в административной части,
		 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
		 * По аналогии с @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
		 */
		if (df_is_admin()) {
			/** @var int|null $storeIdFromRequest */
			$storeIdFromRequest = rm_request('store');
			if ($storeIdFromRequest) {
				$result = rm_store_m()->getStore($result);
			}
			/**
			 * 2015-09-20
			 * При единственном магазине
			 * вызываемый ниже метод метод @uses \Df\Core\State::getStoreProcessed()
			 * возвратит витрину default, однако при нахождении в административной части
			 * нам нужно вернуть витрину admin.
			 * Например, это нужно, чтобы правильно работала функция @used-by df_is_admin()
			 * Переменная $coreCurrentStore в данной точке содержит витрину admin.
			 */
			if (is_null($result) && rm_store_m()->isSingleStoreMode()) {
				$result = rm_store_m()->getStore('admin');
			}
		}
		/**
		 * Теперь смотрим, нельзя ли узнать текущий магазин из веба-адреса в формате РСМ.
		 * Этот формат используют модули 1С:Управление торговлей и Яндекс-Маркет.
		 */
		if (is_null($result)) {
			/**
			 * @uses \Df\Core\State::storeProcessed()
			 * может вызывать @see rm_store() опосредованно: например, через @see df_assert().
			 * Поэтому нам важно отслеживать рекурсию и не зависнуть.
			 */
			/** @var int $recursionLevel */
			static $recursionLevel = 0;
			if (!$recursionLevel) {
				$recursionLevel++;
				try {
					$result = rm_state()->storeProcessed($needThrow = false);
				}
				catch (Exception $e) {
					$recursionLevel--;
					throw $e;
				}
				$recursionLevel--;
			}
		}
		if (is_null($result)) {
			$result = rm_store_m()->getStore();
		}
	}
	return is_object($result) ? $result : rm_store_m()->getStore($result);
}

/**
 * @return \Magento\Store\Model\StoreManagerInterface|\Magento\Store\Model\StoreManager
 */
function rm_store_m() {
	static $r; return $r ? $r : $r = df_o('Magento\Store\Model\StoreManagerInterface');
}


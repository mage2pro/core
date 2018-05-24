<?php
use Df\Directory\Model\Country;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Framework\UrlInterface as U;
use Magento\Store\Api\Data\StoreInterface as IStore;
use Magento\Store\Model\Information as Inf;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface as IStoreManager;
use Magento\Store\Model\StoreResolver;
/**
 * 2015-02-04
 * Обратите внимание, что вряд ли мы вправе кэшировать результат при парметре $store = null,
 * ведь текущий магазин может меняться.
 *
 * 2015-11-04
 * By analogy with @see \Magento\Store\Model\StoreResolver::getCurrentStoreId()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/StoreResolver.php#L82
 *
 * 2015-08-10
 * Доработал алгоритм.
 * Сначала мы смотрим, не находимся ли мы в административной части,
 * и нельзя ли при этом узнать текущий магазин из веб-адреса.
 * By analogy with @see Mage_Adminhtml_Block_Catalog_Product_Grid::_getStore()
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
 * этот метод вернёт витрину по-умолчанию, а не витрину «admin».
 *
 * Не знаю, правильно ли это, то так делает этот метод в Российской сборке для Magento 1.x,
 * поэтому решил пока не менять поведение.
 *
 * В Magento 2 же стандартный метод \Magento\Store\Model\StoreManager::getStore()
 * при вызове без параметров возвращает именно витрину по умолчанию, а не витрину «admin»:
 * https://github.com/magento/magento2/issues/2254
 * «The call for \Magento\Store\Model\StoreManager::getStore() without parameters
 * inside the backend returns the default frontend store, not the «admin» store,
 * which is inconsistent with Magento 1.x behavior and I think it will lead to developer mistakes.»
 *
 * @used-by df_address_store()
 * @param int|string|null|bool|IStore $s [optional]
 * @return IStore|Store
 * @throws \Magento\Framework\Exception\NoSuchEntityException|Exception
 * https://github.com/magento/magento2/issues/2222
 */
function df_store($s = null) {/** @var string|null $c */return
	!is_null($s) ? (is_object($s) ? $s : df_store_m()->getStore($s)) :
		df_store_m()->getStore(!is_null($c = df_request(StoreResolver::PARAM_NAME)) ? $c : (
			// 2017-08-02
			// The store ID specified in the current URL should have priority over the value from the cookie.
			// Violating this rule led us to the following failure:
			// https://github.com/mage2pro/markdown/issues/1
			// Today I was saving a product in the backend, the URL looked like:
			// https://site.com/admin/catalog/product/save/id/45/type/simple/store/0/set/20/key/<key>/back/edit
			// But at the same time I had another store value in the cookie (a frontend store code).
			!is_null($c = df_request('store-view')) ? $c : (
				df_is_backend() ? df_request('store', 'admin') : (
					!is_null($c = df_store_cookie_m()->getStoreCodeFromCookie()) ? $c : null
				)
			)
		))
;}

/**
 * 2016-01-30
 * @used-by df_sentry()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Core\Plugin\Directory\Model\Resource\Country\Collection::aroundLoadByStore()
 * @used-by \Frugue\Core\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @param null|string|int|IScope $store [optional]
 * @return string
 */
function df_store_code($store = null) {return df_scope_code($store);}

/**            
 * 2017-01-21
 * «How to get the store's country?» https://mage2.pro/t/2509
 * @param null|string|int|IStore $store [optional] 
 * @return Country
 */
function df_store_country($store = null) {return df_country(df_store($store)->getConfig(
	Inf::XML_PATH_STORE_INFO_COUNTRY_CODE
));}

/**
 * 2016-01-11
 * @param int|string|null|bool|IStore $store [optional]
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
 * 2017-02-07   
 * @used-by df_scope_stores()
 * @return IStoreManager|StoreManager
 */
function df_store_m() {return df_o(IStoreManager::class);}

/**
 * 2016-01-11
 * @used-by \Dfe\SalesSequence\Config\Next\Element::rows()
 * @param bool $withDefault [optional]
 * @param bool $codeKey [optional]
 * @return string[]
 */
function df_store_names($withDefault = false, $codeKey = false) {return
	array_map(function(IStore $store) {return
		$store->getName()
	;}, df_stores($withDefault, $codeKey))
;}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_store_url_link()
 * @used-by df_store_url_web()
 * @param int|string|null|bool|IStore $s
 * @param string $type
 * @return string
 */
function df_store_url($s, $type) {return df_store($s)->getBaseUrl($type);}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by \Df\Payment\Metadata::vars()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string
 */
function df_store_url_link($s = null) {return df_store_url($s, U::URL_TYPE_LINK);}

/**
 * 2017-03-15
 * Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_domain_current()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string
 */
function df_store_url_web($s = null) {return df_store_url($s, U::URL_TYPE_WEB);}

/**
 * 2016-01-11
 * 2016-01-29
 * Добавил @uses df_ksort(), потому что иначе порядок элементов различается
 * в зависимости от того, загружается ли страница из кэша или нет.
 * Для модуля Dfe\SalesSequence нам нужен фиксированный порядок.     
 * @used-by df_scope_stores()
 * @param bool $withDefault [optional]
 * @param bool $codeKey [optional]
 * @return array|IStore[]
 */
function df_stores($withDefault = false, $codeKey = false) {return df_ksort(
	df_store_m()->getStores($withDefault, $codeKey)
);}
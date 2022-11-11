<?php
use Df\Directory\Model\Country;
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\UrlInterface as U;
use Magento\Sales\Model\Order as O;
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
 * @used-by df_currencies_codes_allowed()
 * @used-by df_currencies_ctn()
 * @used-by df_currency_current()
 * @used-by df_media_path2url()
 * @used-by df_media_url2path()
 * @used-by df_scope_stores()
 * @used-by df_store_country()
 * @used-by df_store_id()
 * @used-by df_store_url()
 * @used-by df_url_frontend()
 * @used-by ikf_pw_api()
 * @used-by \Df\API\Client::__construct()
 * @used-by \Df\API\Facade::__construct()
 * @used-by \Df\API\Facade::s()
 * @used-by \Df\Config\Settings::s()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Dfe\Robokassa\Api\Options::map()
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @param int|string|null|bool|IStore|O $v [optional]
 * @return IStore|Store
 * @throws NSE|\Exception
 * https://github.com/magento/magento2/issues/2222
 */
function df_store($v = null) {/** @var string|null $c */return
	!is_null($v) ? (df_is_o($v) ? $v->getStore() : (is_object($v) ? $v : df_store_m()->getStore($v))) :
		df_store_m()->getStore(!is_null($c = df_request(StoreResolver::PARAM_NAME)) ? $c : (
			# 2017-08-02
			# The store ID specified in the current URL should have priority over the value from the cookie.
			# Violating this rule led us to the following failure:
			# https://github.com/mage2pro/markdown/issues/1
			# Today I was saving a product in the backend, the URL looked like:
			# https://site.com/admin/catalog/product/save/id/45/type/simple/store/0/set/20/key/<key>/back/edit
			# But at the same time I had another store value in the cookie (a frontend store code).
			!is_null($c = df_request('store-view')) ? $c : (
				df_is_backend() ? df_request('store', 'admin') : (
					!is_null($c = df_store_cookie_m()->getStoreCodeFromCookie()) ? $c : null
				)
			)
		))
;}

/**
 * 2016-01-30
 * @used-by df_replace_store_code_in_url()
 * @used-by df_sentry()
 * @used-by \Frugue\Core\Plugin\Directory\Model\Resource\Country\Collection::aroundLoadByStore()
 * @used-by \Frugue\Shipping\Header::_toHtml()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @used-by \Frugue\Store\Switcher::params()
 * @param null|string|int|IScope $s [optional]
 */
function df_store_code($s = null):string {return df_scope_code($s);}

/**
 * 2020-01-18
 * @see df_store_names()
 * @used-by df_store_code_from_url()
 * @return string[]
 */
function df_store_codes():array {return dfcf(function() {return array_map(
	function(IStore $s) {return $s->getCode();}, df_stores()
);});}

/**            
 * 2017-01-21
 * «How to get the store's country?» https://mage2.pro/t/2509
 * @param null|string|int|IStore $store [optional]
 */
function df_store_country($store = null):Country {return df_country(df_store($store)->getConfig(
	Inf::XML_PATH_STORE_INFO_COUNTRY_CODE
));}

/**
 * 2016-01-11
 * @used-by df_category()
 * @used-by df_product()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\AddToCart::execute()
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @param int|string|null|bool|IStore $store [optional]
 */
function df_store_id($store = null):int {return df_store($store)->getId();}

/**
 * 2016-01-11
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::afterCommitCallback()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::nextNumbersFromDb()
 * @param bool $withDefault [optional]
 * @return int[]
 */
function df_store_ids($withDefault = false):array {return array_keys(df_stores($withDefault));}

/**
 * 2017-02-07              
 * @used-by df_scope_stores()  
 * @used-by df_store()
 * @used-by df_stores()
 * @used-by df_website() 
 * @return IStoreManager|StoreManager
 */
function df_store_m() {return df_o(IStoreManager::class);}

/**
 * 2016-01-11
 * @see df_store_codes()  
 * @see df_category_names()
 * @used-by \Dfe\SalesSequence\Config\Next\Element::rows()
 * @param bool $withDefault [optional]
 * @param bool $codeKey [optional]
 * @return string[]
 */
function df_store_names($withDefault = false, $codeKey = false) {return array_map(
	function(IStore $store) {return $store->getName();}, df_stores($withDefault, $codeKey)
);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by df_store_url_link()
 * @used-by df_store_url_web()
 * @param int|string|null|bool|IStore $s
 * @param string $type
 * @return string
 */
function df_store_url($s, $type) {return df_store($s)->getBaseUrl($type);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * @used-by \Df\Payment\Metadata::vars()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string
 */
function df_store_url_link($s = null) {return df_store_url($s, U::URL_TYPE_LINK);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
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
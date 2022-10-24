<?php
use Df\Config\Settings;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface as IConfig;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Config\Model\ResourceModel\Config as RConfig;
use Magento\Store\Model\ScopeInterface as SS;
use Magento\Store\Model\Store;
/**
 * @uses \Magento\Framework\App\Config\Data::getValue()
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
 *
 * 2015-12-26
 * https://mage2.pro/t/357
 * «The @uses \Magento\Framework\App\Config::getValue() method
 * has a wrong PHPDoc type for the $scopeCode parameter».
 *
 * Метод возвращает null или $default, если данные отсутствуют:
 * @see \Magento\Framework\App\Config\Data::getValue()
 * https://github.com/magento/magento2/blob/6ce74b2/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
 *
 * 2015-10-09
 * https://mage2.pro/t/128
 * https://github.com/magento/magento2/issues/2064
 *
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 *      
 * 2017-10-22
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L17
 * @used-by df_mail()
 * @used-by ikf_ite()
 * @used-by \BlushMe\Checkout\Block\Extra::items()
 * @used-by \CanadaSatellite\Core\Plugin\Cart2Quote\DeskEmail\Observer\Frontend\SaveMessageAfterObserver::beforeExecute(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/54)
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Rss\Controller\Feed::beforeExecute(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/43)
 * @used-by \CanadaSatellite\Core\Plugin\Mageplaza\Blog\Helper\Data::afterGetBlogUrl() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/43)
 * @used-by \Df\AdobeStockImageAdminUi\Plugin\Model\Listing\DataProvider::aroundGetData()
 * @used-by \Df\Config\Comment::sibling()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Df\Security\BlackList::load()
 * @used-by \Df\Shipping\Settings::enable()
 * @used-by \Dfe\Portal\Block\Content::getTemplate()
 * @used-by app/code/Interactivated/Quotecheckout/view/frontend/templates/dashboard/onepage/billing.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @used-by app/code/Interactivated/Quotecheckout/view/frontend/templates/dashboard/onepage/billing/sortbilling.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @used-by app/code/Interactivated/Quotecheckout/view/frontend/templates/dashboard/onepage/shipping/sortshipping.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/116)
 * @param string|string[] $k
 * @param null|string|int|ScopeA|Store|IConfigData|ConfigData|array(int|string) $scope [optional]
 * @param mixed|callable $d [optional]
 * @return array|string|null|mixed
 */
function df_cfg($k, $scope = null, $d = null) {
	/** 2018-04-21 @used-by \Df\Shipping\Settings::enable() */
	if (is_array($k)) {
		$k = df_cc_path($k);
	}
	/** @var array|string|null|mixed $r */
	$r = $scope instanceof IConfigData ? $scope->getValue($k) : df_cfg_m()->getValue($k, ...(
		is_array($scope) ? [$scope[0], $scope[1]] : [SS::SCOPE_STORE, $scope])
	);
	return df_if(df_cfg_empty($r), $d, $r);
}

/**
 * 2016-08-03
 * @see df_cfg_save()
 * @param string $path		E.g.: «web/unsecure/base_url»
 * @param string $scope		E.g.: «default»
 * @param int $scopeId		E.g.: «0»
 */
function df_cfg_delete($path, $scope, $scopeId):void {df_cfg_r()->deleteConfig($path, $scope, $scopeId);}

/**
 * 2016-11-12
 * @used-by df_cfg()
 * @used-by \Df\Config\Settings::vv()
 * @param array|string|null|mixed $v
 */
function df_cfg_empty($v):bool {return is_null($v) || '' === $v;}

/**
 * 2016-02-09
 * https://mage2.pro/t/639
 * The default implementation of the @see \Magento\Framework\App\Config\ScopeConfigInterface
 * is @see \Magento\Framework\App\Config
 * @used-by df_cfg()
 * @return IConfig|Config
 */
function df_cfg_m() {return df_o(IConfig::class);}

/**
 * 2016-08-03
 * @used-by df_cfg_delete()
 * @used-by df_cfg_save()
 */
function df_cfg_r():RConfig {return df_o(RConfig::class);}

/**
 * 2016-08-03 How to save a config option programmatically? https://mage2.pro/t/289    
 * 2017-10-22
 * Note 1.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L19
 * Note 2.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L15
 * Note 3.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L17
 * Note 4.
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORES constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L13
 * @see \Magento\Store\Model\ScopeInterface::SCOPE_STORES 
 * @see \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
 * @see df_cfg_delete()
 * @used-by df_cfg_save_cc()
 * @used-by \Dfe\Dynamics365\Settings\General\OAuth::refreshTokenSave()
 * @param string $path		E.g.: «web/unsecure/base_url»
 * @param string $v
 * @param string $scope [optional]		«default», «websites», «website», «stores», «store»
 * @param int $scopeId [optional]		E.g.: «0»
 */
function df_cfg_save($path, $v, $scope = 'default', $scopeId = 0) {df_cfg_r()->saveConfig($path, $v, dftr($scope, [
	SS::SCOPE_WEBSITE => SS::SCOPE_WEBSITES, SS::SCOPE_STORE => SS::SCOPE_STORES
]), $scopeId);}

/**
 * 2021-09-22
 * @used-by \Df\Security\BlackList::save()
 * @param string $path
 * @param string $v
 * @param string $scope [optional]
 * @param int $scopeId [optional]
 */
function df_cfg_save_cc($path, $v, $scope = 'default', $scopeId = 0) {
	df_cfg_save($path, $v, $scope, $scopeId);
	df_cache_clean_cfg();
}

/**
 * 2018-01-28
 * @used-by \Df\Core\TestCase::s()
 * @used-by \Df\Sso\CustomerReturn::execute()
 * @used-by \Df\Zoho\API\Client::ss()
 * @used-by \Df\Zoho\App::ss()
 * @used-by \Df\Framework\Mail\TransportObserver::execute()
 * @param object|string $m
 * @return Settings
 */
function dfs($m) {return Settings::convention(df_module_name_c($m));}
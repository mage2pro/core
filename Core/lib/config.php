<?php
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface as IConfig;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\Config\ScopePool;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\ScopeInterface as ScopeS;
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
 * @param string $key
 * @param null|string|int|ScopeA|Store|IConfigData|ConfigData $scope [optional]
 * @param mixed|callable $default [optional]
 * @return array|string|null|mixed
 */
function df_cfg($key, $scope = null, $default = null) {
	/** @var array|string|null|mixed $result */
	$result =
		$scope instanceof IConfigData
		? $scope->getValue($key)
		: df_cfg_m()->getValue($key, ScopeS::SCOPE_STORE, $scope)
	;
	return df_if(is_null($result) || '' === $result, $default, $result);
}

/**
 * 2016-02-09
 * https://mage2.pro/t/639
 * The default implementation of the @see \Magento\Framework\App\Config\ScopeConfigInterface
 * is @see \Magento\Framework\App\Config
 * @return IConfig|Config
 */
function df_cfg_m() {return df_o(IConfig::class);}

/**
 * 2016-07-30
 * @param int|string|null|ScopeA|Store $scope
 * @param string $scopeType [optional]
 * @return IConfigData|ConfigData
 */
function df_scope($scope, $scopeType = ScopeS::SCOPE_STORE) {
	return df_scope_pool()->getScope($scopeType, $scope);
}

/**
 * 2015-12-26
 * https://mage2.pro/t/359
 * «Propose to make the @see \Magento\Framework\App\Config\ScopePool::_getScopeCode() public
 * because it is useful to calculate cache keys based on a scope
 * (like @see \Magento\Framework\App\Config\ScopePool::getScope() does)».
 *
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @param string|null $scopeType [optional]
 * @param ScopePool $scopePool [optional]
 * @return string
 */
function df_scope_code($scope = null, $scopeType = ScopeS::SCOPE_STORE, ScopePool $scopePool = null) {
	return \Df\Framework\App\Config\ScopePool::code($scope, $scopeType, $scopePool);
}

/**
 * 2016-07-30
 * @return ScopePool
 */
function df_scope_pool() {return df_o(ScopePool::class);}



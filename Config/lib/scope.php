<?php
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\Config\ScopePool;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\ScopeInterface as ScopeS;
use Magento\Store\Model\Store;
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



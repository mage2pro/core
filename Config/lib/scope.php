<?php
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Framework\App\ScopeResolverPool;
use Magento\Store\Model\ScopeInterface as SS;
use Magento\Store\Model\Store;
/**
 * 2017-06-29
 * @used-by \Dfe\Dynamics365\Button::onFormInitialized()
 * @return array(string, int)
 */
function df_scope() {
	/** @var array(string, int) $result */
	$result = null;
	foreach ([SS::SCOPE_WEBSITE => SS::SCOPE_WEBSITES, SS::SCOPE_STORE => SS::SCOPE_STORES] as $s => $ss) {
		/** @var string $scope */
		if (!is_null($id = df_request($s))) {
			$result = [$ss, $id];
			break;
		}
	}
	return $result ?: ['default', 0];
}

/**
 * 2015-12-26
 * https://mage2.pro/t/359
 * «Propose to make the @see \Magento\Framework\App\Config\ScopePool::_getScopeCode() public
 * because it is useful to calculate cache keys based on a scope
 * (like @see \Magento\Framework\App\Config\ScopePool::getScope() does)».
 *
 * 2015-12-26
 * Сделал для $scopeType именно такое значение по умолчанию для согласованности с
 * @see \Df\Config\Settings::v()
 * https://mage2.pro/t/128
 * https://github.com/magento/magento2/issues/2064
 *
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @param string $scopeType [optional]
 * @return string
 */
function df_scope_code($scope = null, $scopeType = SS::SCOPE_STORE) {
	if (($scope === null || is_numeric($scope)) && $scopeType !== IScopeConfig::SCOPE_TYPE_DEFAULT) {
		$scope = df_scope_resolver_pool()->get($scopeType)->getScope($scope);
	}
	return $scope instanceof ScopeA ? $scope->getCode() : $scope;
}

/**
 * 2016-12-16
 * @return ScopeResolverPool
 */
function df_scope_resolver_pool() {return df_o(ScopeResolverPool::class);}
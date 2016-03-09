<?php
use Magento\Framework\App\Config;
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
 * @param null|string|int|ScopeA|Store $scope [optional]
 * @param mixed|callable $default [optional]
 * @return array|string|null|mixed
 */
function df_cfg($key, $scope = null, $default = null) {
	$result = df_cfg_m()->getValue($key, ScopeS::SCOPE_STORE, $scope);
	return df_if(is_null($result) || '' === $result, $default, $result);
}

/**
 * 2016-02-09
 * https://mage2.pro/t/639
 * The default implementation of the @see \Magento\Framework\App\Config\ScopeConfigInterface
 * is @see \Magento\Framework\App\Config
 * @return Config\ScopeConfigInterface|\Magento\Framework\App\Config
 */
function df_cfg_m() {return df_o(Config\ScopeConfigInterface::class);}



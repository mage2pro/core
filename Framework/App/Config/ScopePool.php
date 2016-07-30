<?php
namespace Df\Framework\App\Config;
use Magento\Framework\App\Config\ScopePool as _ScopePool;
use Magento\Framework\App\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface as IStore;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use Magento\Store\Model\Store;
/**
 * 2015-12-26
 * Цель нашего класса — открыть публичный доступ к методу
 * @see \Magento\Framework\App\Config\ScopePool::_getScopeCode().
 *
 * https://mage2.pro/t/359
 * «Propose to make the @see \Magento\Framework\App\Config\ScopePool::_getScopeCode() public
 * because it is useful to calculate cache keys based on a scope
 * (like @see \Magento\Framework\App\Config\ScopePool::getScope() does)».
 *
 * Мы наследуемся не для того, чтобы перекрыть родительский метод,
 * а чтобы таким хитрым способом получить доступ к области protected класса
 * @uses \Magento\Framework\App\Config\ScopePool
 */
class ScopePool extends _ScopePool {
	/**
	 * 2015-12-26
	 * @param null|string|int|ScopeInterface $scope [optional]
	 * @param string|null $scopeType [optional]
	 * @param _ScopePool $scopePool [optional]
	 * @return string
	 */
	public static function code(
		$scope = null, $scopeType = StoreScopeInterface::SCOPE_STORE, _ScopePool $scopePool = null
	) {
		/**
		 * 2015-12-26
		 * @see \Magento\Framework\App\Config::__construct()
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Config.php#L24-L30
		 */
		if (!$scopePool) {
			$scopePool = df_scope_pool();
		}
		return $scopePool->_getScopeCode(
			/**
			 * 2015-12-26
			 * Сделал именно такое значение по умолчанию для согласованности с
			 * @see \Df\Core\Settings::v()
			 * https://mage2.pro/t/128
			 * https://github.com/magento/magento2/issues/2064
			 */
			is_null($scopeType) ? StoreScopeInterface::SCOPE_STORE : $scopeType
			, $scope
		);
	}
}
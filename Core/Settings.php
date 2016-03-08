<?php
namespace Df\Core;
use Df\Config\A;
use Df\Typography\Font;
use Magento\Framework\App\Config;
use Magento\Framework\App\ScopeInterface;
use Magento\Store\Model\Store;
class Settings extends O {
	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return int
	 */
	public function b($key, $scope = null) {return df_bool($this->v($key, $scope));}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return int
	 */
	public function i($key, $scope = null) {return df_int($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return int
	 */
	public function nat($key, $scope = null) {return df_nat($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return int
	 */
	public function nat0($key, $scope = null) {return df_nat0($this->v($key, $scope));}

	/**
	 * 2015-12-07
	 * Скорректировал метод таким образом,
	 * чтобы при отсутствии значения опции он возвращал null
	 * (а не делал decrypt для значения null или пустой строки).
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return string|null
	 */
	public function p($key, $scope = null) {
		/** @var string|mixed $result */
		$result = $this->v($key, $scope);
		return !$result ? null : df_encryptor()->decrypt($result);
	}

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
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @param mixed|callable $default [optional]
	 * @return array|string|null|mixed
	 */
	public function v($key, $scope = null, $default = null) {
		$result = $this->config()->getValue(
			$this->prefix() . $key
			/**
			 * 2015-10-09
			 * https://mage2.pro/t/128
			 * https://github.com/magento/magento2/issues/2064
			 */
			, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
			, $scope
		);
		return df_if(is_null($result) || '' === $result, $default, $result);
	}

	/**
	 * 2015-12-30
	 * @param string $key
	 * @param string $itemClass
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return A
	 */
	protected function _a($key, $itemClass, $scope = null) {
		/** @var string $scopeCode */
		$scope = df_scope_code($scope);
		/** @var string $cacheKey */
		$cacheKey = implode('::', [$key, $itemClass, $scope]);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			$this->{__METHOD__}[$cacheKey] = A::i($itemClass, $this->json($key, $scope));
		}
		return $this->{__METHOD__}[$cacheKey];
	}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return Font
	 */
	protected function _font($key, $scope = null) {
		/** @var string $scopeCode */
		$scopeCode = df_scope_code($scope);
		if (!isset($this->{__METHOD__}[$key][$scopeCode])) {
			$this->{__METHOD__}[$key][$scopeCode] = new Font($this->json($key, $scope));
		}
		return $this->{__METHOD__}[$key][$scopeCode];
	}

	/**
	 * 2016-01-29
	 * @param string $key
	 * @param int $i Номер строки
	 * @param int $j Номер столбца
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @param string|null $default [optonal]
	 * @return Font
	 */
	protected function _matrix($key, $i, $j, $scope = null, $default = null) {
		/** @var string $scopeCode */
		$scopeCode = df_scope_code($scope);
		if (!isset($this->{__METHOD__}[$key][$scopeCode])) {
			$this->{__METHOD__}[$key][$scopeCode] = $this->json($key, $scope);
		}
		return dfa(dfa($this->{__METHOD__}[$key][$scopeCode], $i, []), $j, $default);
	}

	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return $this[self::$P__PREFIX];}

	/**
	 * 2016-02-09
	 * https://mage2.pro/t/639
	 * The default implementation of the @see \Magento\Framework\App\Config\ScopeConfigInterface
	 * is @see \Magento\Framework\App\Config
	 * @return Config\ScopeConfigInterface|\Magento\Framework\App\Config
	 */
	private function config() {return df_o(Config\ScopeConfigInterface::class);}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @param null|string|int|ScopeInterface|Store $scope [optional]
	 * @return mixed[]
	 */
	private function json($key, $scope = null) {
		return df_nta(@df_json_decode($this->v($key, $scope)));
	}

	/**
	 * 2016-02-09
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__PREFIX, RM_V_STRING);
	}

	/** @var string */
	private static $P__PREFIX = 'prefix';

	/**
	 * 2016-02-09
	 * @param string $prefix
	 * @return $this
	 */
	public static function sp($prefix) {
		static $cache;
		if (!isset($cache[$prefix])) {
			$cache[$prefix] = new self([self::$P__PREFIX => $prefix]);
		}
		return $cache[$prefix];
	}
}
<?php
namespace Df\Core;
use Df\Config\A;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Typography\Font;
use Magento\Framework\App\Config;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
class Settings extends O {
	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return int
	 */
	public function b($key, $scope = null) {return df_bool($this->v($key, $scope));}

	/**
	 * 2016-03-09
	 * Может возвращать строку или false.
	 * @used-by \Dfe\Stripe\Settings::prefill()
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return string|false
	 */
	public function bv($key, $scope = null) {return $this->v($key, $scope) ?: false;}

	/**
	 * 2016-03-14
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return string[]
	 */
	public function csv($key, $scope = null) {return df_csv_parse($this->v($key, $scope));}

	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return int
	 */
	public function i($key, $scope = null) {return df_int($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return int
	 */
	public function nat($key, $scope = null) {return df_nat($this->v($key, $scope));}

	/**
	 * 2015-12-26
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return int
	 */
	public function nat0($key, $scope = null) {return df_nat0($this->v($key, $scope));}

	/**
	 * 2015-12-07
	 * Скорректировал метод таким образом,
	 * чтобы при отсутствии значения опции он возвращал null
	 * (а не делал decrypt для значения null или пустой строки).
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @return string|null
	 */
	public function p($key, $scope = null) {
		/** @var string|mixed $result */
		$result = $this->v($key, $scope);
		return !$result ? null : df_encryptor()->decrypt($result);
	}

	/**
	 * 2016-03-08
	 * @param null|string|int|S|Store $scope
	 * @return void
	 */
	public function setScope($scope) {$this->_scope = $scope;}

	/**
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @param mixed|callable $default [optional]
	 * @return array|string|null|mixed
	 */
	public function v($key, $scope = null, $default = null) {
		return df_cfg($this->prefix() . $key, $this->scope($scope), $default);
	}

	/**
	 * 2015-12-30
	 * @param string $key
	 * @param string $itemClass
	 * @param null|string|int|S|Store $scope [optional]
	 * @return A
	 */
	protected function _a($key, $itemClass, $scope = null) {
		$scope = df_scope_code($this->scope($scope));
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
	 * @param null|string|int|S|Store $scope [optional]
	 * @return Font
	 */
	protected function _font($key, $scope = null) {
		$scope = df_scope_code($this->scope($scope));
		if (!isset($this->{__METHOD__}[$key][$scope])) {
			$this->{__METHOD__}[$key][$scope] = new Font($this->json($key, $scope));
		}
		return $this->{__METHOD__}[$key][$scope];
	}

	/**
	 * 2016-01-29
	 * @param string $key
	 * @param int $i Номер строки
	 * @param int $j Номер столбца
	 * @param null|string|int|S|Store $scope [optional]
	 * @param string|null $default [optonal]
	 * @return Font
	 */
	protected function _matrix($key, $i, $j, $scope = null, $default = null) {
		$scope = df_scope_code($this->scope($scope));
		if (!isset($this->{__METHOD__}[$key][$scope])) {
			$this->{__METHOD__}[$key][$scope] = $this->json($key, $scope);
		}
		return dfa(dfa($this->{__METHOD__}[$key][$scope], $i, []), $j, $default);
	}

	/**
	 * 2016-05-13
	 * @param string $key
	 * @param string $suffix
	 * @param string $value
	 * @param null|string|int|S|Store $s [optional]
	 * @return string[]
	 */
	protected function nwb($key, $suffix, $value, $s = null) {
		return NWB::is($this->v($key, $s), $value, $this->csv($key . '_' . $suffix, $s));
	}

	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return $this[self::$P__PREFIX];}

	/**
	 * 2016-03-08
	 * @param null|string|int|S|Store $scope [optional]
	 * @return null|string|int|S|Store $scope [optional]
	 */
	protected function scope($scope) {return !is_null($scope) ? $scope : $this->_scope;}

	/**
	 * 2015-12-16
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
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

	/**
	 * 2016-03-08
	 * @used-by \Df\Core\Settings::scope()
	 * @used-by \Df\Core\Settings::setScope()
	 * @var null|string|int|S|Store
	 */
	private $_scope;

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
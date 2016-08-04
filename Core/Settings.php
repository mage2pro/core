<?php
namespace Df\Core;
use Df\Config\A;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Typography\Font;
use Magento\Framework\App\Config;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
/** @method static Settings s() */
abstract class Settings extends O {
	/**
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	abstract protected function prefix();

	/**
	 * 2015-11-09
	 * @param string $key
	 * @param null|string|int|S|Store $scope [optional]
	 * @param bool $default [optional]
	 * @return int
	 */
	public function b($key, $scope = null, $default = false) {
		return df_bool($this->v($key, $scope, $default));
	}

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
	 * 2016-08-04
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function enable($s = null) {return $this->b(__FUNCTION__, $s);}

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
	 * I have corrected the method, so it now returns null for an empty value
	 * (avoids to descrypt a null-value or an empty string).
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
			/** @var array(string => mixed) $items */
			$items = !$this->enable($scope) ? [] : $this->json($key, $scope);
			$this->{__METHOD__}[$cacheKey] = A::i($itemClass, $items);
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
	 * 2016-07-31
	 * @param string $class
	 * @return Settings
	 */
	protected function child($class) {
		if (!isset($this->{__METHOD__}[$class])) {
			/**
			 * 2015-08-04
			 * Ошибочно писать здесь self::s($class)
			 * потому что класс ребёнка не обязательно должен быть наследником класса родителя:
			 * ему достаточно быть наследником @see \Df\Core\Settings
			 * @var Settings $result
			 */
			$result = df_sc($class, __CLASS__);
			$result->setScope($this->scope());
			$this->{__METHOD__}[$class] = $result;
		}
		return $this->{__METHOD__}[$class];
	}

	/**
	 * 2016-05-13
	 * 2016-06-09
	 * Если опция не задана, но метод возвращает «да».
	 * Если опция задана, то смотрим уже тип ограничения: белый или чёрный список.
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
	 * 2016-06-09
	 * Если опция не задана, но метод возвращает «нет».
	 * Если опция задана, то смотрим уже тип ограничения: белый или чёрный список.
	 * @param string $key
	 * @param string $suffix
	 * @param string $value
	 * @param null|string|int|S|Store $s [optional]
	 * @return string[]
	 */
	protected function nwbn($key, $suffix, $value, $s = null) {
		return NWB::isNegative($this->v($key, $s), $value, $this->csv($key . '_' . $suffix, $s));
	}

	/**
	 * 2016-03-08
	 * @param null|string|int|S|Store $scope [optional]
	 * @return null|string|int|S|Store
	 */
	protected function scope($scope = null) {return !is_null($scope) ? $scope : $this->_scope;}

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
	 * 2016-03-08
	 * @used-by \Df\Core\Settings::scope()
	 * @used-by \Df\Core\Settings::setScope()
	 * @var null|string|int|S|Store
	 */
	private $_scope;
}
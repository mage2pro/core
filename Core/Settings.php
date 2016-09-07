<?php
namespace Df\Core;
use Df\Config\A as ConfigA;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Typography\Font;
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
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param bool $default [optional]
	 * @return int
	 */
	public function b($key = null, $s = null, $default = false) {
		return df_bool($this->v($key ?: df_caller_f(), $s, $default));
	}

	/**
	 * 2016-03-09
	 * Может возвращать строку или false.
	 * @used-by \Dfe\Stripe\Settings::prefill()
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string|false
	 */
	public function bv($key = null, $s = null) {return $this->v($key ?: df_caller_f(), $s) ?: false;}

	/**
	 * 2016-03-14
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string[]
	 */
	public function csv($key = null, $s = null) {
		return df_csv_parse($this->v($key ?: df_caller_f(), $s));
	}

	/**
	 * 2016-08-04
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function enable($s = null) {return $this->b(null, $s);}

	/**
	 * 2015-11-09
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return int
	 */
	public function i($key = null, $s = null) {return df_int($this->v($key ?: df_caller_f(), $s));}

	/**
	 * 2015-12-26
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return int
	 */
	public function nat($key = null, $s = null) {return df_nat($this->v($key ?: df_caller_f(), $s));}

	/**
	 * 2015-12-26
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return int
	 */
	public function nat0($key = null, $s = null) {return df_nat0($this->v($key ?: df_caller_f(), $s));}

	/**
	 * 2015-12-07
	 * I have corrected the method, so it now returns null for an empty value
	 * (avoids to decrypt a null-value or an empty string).
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string|null
	 */
	public function p($key = null, $s = null) {
		/** @var string|mixed $result */
		$result = $this->v($key ?: df_caller_f(), $s);
		return !$result ? null : df_encryptor()->decrypt($result);
	}

	/**
	 * 2016-03-08
	 * @param null|string|int|S|Store $s
	 * @return void
	 */
	public function setScope($s) {$this->_scope = $s;}

	/**
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param mixed|callable $default [optional]
	 * @return array|string|null|mixed
	 */
	public function v($key = null, $s = null, $default = null) {
		return df_cfg($this->prefix() . ($key ?: df_caller_f()), $this->scope($s), $default);
	}

	/**
	 * 2015-12-30
	 * @param string|null $key [optional]
	 * @param string $itemClass
	 * @param null|string|int|S|Store $s [optional]
	 * @return ConfigA
	 */
	protected function _a($itemClass, $key = null, $s = null) {
		$key = $key ?: df_caller_f();
		$s = df_scope_code($this->scope($s));
		/** @var string $cacheKey */
		$cacheKey = df_ckey($key, $itemClass, $s);
		if (!isset($this->{__METHOD__}[$cacheKey])) {
			/** @var array(string => mixed) $items */
			$items = !$this->enable($s) ? [] : $this->json($key, $s);
			$this->{__METHOD__}[$cacheKey] = ConfigA::i($itemClass, $items);
		}
		return $this->{__METHOD__}[$cacheKey];
	}

	/**
	 * 2015-12-16
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return Font
	 */
	protected function _font($key = null, $s = null) {
		$key = $key ?: df_caller_f();
		$s = df_scope_code($this->scope($s));
		if (!isset($this->{__METHOD__}[$key][$s])) {
			$this->{__METHOD__}[$key][$s] = new Font($this->json($key, $s));
		}
		return $this->{__METHOD__}[$key][$s];
	}

	/**
	 * 2016-01-29
	 * @param int $i Номер строки
	 * @param int $j Номер столбца
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param string|null $default [optonal]
	 * @return Font
	 */
	protected function _matrix($i, $j, $key = null, $s = null, $default = null) {
		$key = $key ?: df_caller_f();
		$s = df_scope_code($this->scope($s));
		if (!isset($this->{__METHOD__}[$key][$s])) {
			$this->{__METHOD__}[$key][$s] = $this->json($key, $s);
		}
		return dfa(dfa($this->{__METHOD__}[$key][$s], $i, []), $j, $default);
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
	 * @param string $suffix
	 * @param string $value
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string[]
	 */
	protected function nwb($suffix, $value, $key = null, $s = null) {
		$key = $key ?: df_caller_f();
		return NWB::is($this->v($key, $s), $value, $this->csv($key . '_' . $suffix, $s));
	}

	/**
	 * 2016-06-09
	 * Если опция не задана, но метод возвращает «нет».
	 * Если опция задана, то смотрим уже тип ограничения: белый или чёрный список.
	 * @param string $suffix
	 * @param string $value
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return string[]
	 */
	protected function nwbn($suffix, $value, $key = null, $s = null) {
		$key = $key ?: df_caller_f();
		return NWB::isNegative($this->v($key, $s), $value, $this->csv($key . '_' . $suffix, $s));
	}

	/**
	 * 2016-03-08
	 * @param null|string|int|S|Store $s [optional]
	 * @return null|string|int|S|Store
	 */
	protected function scope($s = null) {return !is_null($s) ? $s : $this->_scope;}

	/**
	 * 2015-12-16
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return mixed[]
	 */
	private function json($key = null, $s = null) {
		return df_nta(@df_json_decode($this->v($key ?: df_caller_f(), $s)));
	}

	/**
	 * 2016-03-08
	 * @used-by \Df\Core\Settings::scope()
	 * @used-by \Df\Core\Settings::setScope()
	 * @var null|string|int|S|Store
	 */
	private $_scope;
}
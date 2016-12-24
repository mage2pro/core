<?php
namespace Df\Config;
use Df\Config\A as ConfigA;
use Df\Config\Source\NoWhiteBlack as NWB;
use Df\Typography\Font;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
/** @method static Settings s() */
abstract class Settings extends O {
	/**
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 * 2016-11-24
	 * Отныне значение должно быть без слеша на конце.
	 */
	abstract protected function prefix();

	/**
	 * 2015-11-09
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param bool $d [optional]
	 * @return int
	 */
	public function b($key = null, $s = null, $d = false) {return
		df_bool($this->v($key ?: df_caller_f(), $s, $d))
	;}

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
	public function csv($key = null, $s = null) {return
		df_csv_parse($this->v($key ?: df_caller_f(), $s))
	;}

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
	 * @param mixed|callable $d [optional]
	 * @return array|string|null|mixed
	 */
	public function v($key = null, $s = null, $d = null) {return
		df_cfg($this->prefix() . '/' . self::phpNameToKey($key ?: df_caller_f()), $this->scope($s), $d)
	;}

	/**
	 * 2015-12-30
	 * @param string|null $key [optional]
	 * @param string $itemClass
	 * @param null|string|int|S|Store $s [optional]
	 * @return ConfigA
	 */
	protected function _a($itemClass, $key = null, $s = null) {return
		dfcf(function($itemClass, $key, $s) {return
			ConfigA::i($itemClass, !$this->enable($s) ? [] : $this->json($key, $s))
		;}, [$itemClass, $key ?: df_caller_f(), df_scope_code($this->scope($s))])
	;}

	/**
	 * 2015-12-16
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @return Font
	 */
	protected function _font($key = null, $s = null) {return dfc($this, function($key, $s) {return
		new Font($this->json($key, $s))
	;}, [$key ?: df_caller_f(), df_scope_code($this->scope($s))]);}

	/**
	 * 2016-01-29
	 * @param int $i Номер строки
	 * @param int $j Номер столбца
	 * @param string|null $key [optional]
	 * @param null|string|int|S|Store $s [optional]
	 * @param string|null $d [optonal]
	 * @return Font
	 */
	protected function _matrix($i, $j, $key = null, $s = null, $d = null) {return
		dfa(dfa(dfc($this, function($key, $s) {return
			$this->json($key, $s)
		;}, [$key ?: df_caller_f(), df_scope_code($this->scope($s))]), $i, []), $j, $d)
	;}

	/**
	 * 2016-07-31
	 * @param string $class
	 * @return Settings
	 */
	protected function child($class) {return dfc($this, function($class) {
		/**
		 * 2015-08-04
		 * Ошибочно писать здесь self::s($class)
		 * потому что класс ребёнка не обязательно должен быть наследником класса родителя:
		 * ему достаточно быть наследником @see \Df\Config\Settings
		 * @var Settings $result
		 */
		$result = df_sc($class, __CLASS__);
		$result->setScope($this->scope());
		return $result;
	}, func_get_args());}

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
	private function json($key = null, $s = null) {return
		df_nta(@df_json_decode($this->v($key ?: df_caller_f(), $s)))
	;}

	/**
	 * 2016-03-08
	 * @used-by \Df\Config\Settings::scope()
	 * @used-by \Df\Config\Settings::setScope()
	 * @var null|string|int|S|Store
	 */
	private $_scope;

	/**
	 * 2016-08-04
	 * 2016-11-25
	 * Отныне метод возвращает класс не обязательно из базовой папки (например, \Df\Sso\Settings),
	 * а из папки с тем же окончанием, что и у вызываемого класса.
	 * Например, \Df\Sso\Settings\Button::convention() будет искать класс в папке Settings\Button
	 * модуля, к которому относится класс $c.
	 * @param object|string $c
	 * @param string $key [optional]
	 * @param null|string|int|S $scope [optional]
	 * @param mixed|callable $d [optional]
	 * @return self
	 */
	public static function convention($c, $key = '', $scope = null, $d = null) {
		/** @var self $result */
		/**
		 * 2016-11-25
		 * Используем 2 уровня кэширования, и оба они важны:
		 * 1) Кэширование self::s() приводит к тому, что вызов s() непосредственно для класса
		 * возвращает тот же объект, что и вызов convention(). Это очень важно.
		 * 2) Кэширование dfcf() позволяет нам не рассчитывать df_con_heir()
		 * при каждом вызове convention().
		 */
		$result = dfcf(function($c, $def) {return
			self::s(df_con_heir($c, $def))
		;}, [df_cts($c), static::class]);
		return df_null_or_empty_string($key) ? $result : $result->v($key, $scope, $d);
	}

	/**
	 * 2016-12-20
	 * Возвращает класс из базовой папки (например, \Df\Sso\Settings).
	 * модуля, к которому относится класс $c.
	 * @param object|string $c
	 * @return self
	 */
	public static function conventionB($c) {return
		self::s(df_ar(df_con($c, 'Settings'), static::class))
	;}

	/**
	 * 2016-12-23
	 * Теперь ключи могут начинаться с цифры (например: «3DS»).
	 * Методы PHP для таких ключей будут содержать приставку «_».
	 * Например, ключам «test3DS» и «live3DS» соответствует метод
	 * @see \Dfe\Omise\Settings::_3DS()
	 * @used-by v()
	 * @used-by \Df\Payment\Settings::testableGeneric()
	 * @param string $name
	 * @return string
	 */
	protected static function phpNameToKey($name) {return df_trim_left($name, '_');}
}
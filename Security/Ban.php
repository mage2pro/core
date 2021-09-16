<?php
namespace Df\Security;
# 2021-09-16
# "Implement an ability to temporary ban visitors with a particular IP address": https://github.com/mage2pro/core/issues/159
final class BlackList {
	/**
	 * 2021-09-16
	 * @used-by df_ban()
	 * @param string $ip [optional]
	 */
	function add($ip = null) {$this->save([$this->ip($ip) => time() + (60 * 60 * 24 * 30)] + $this->load());}

	/**
	 * 2021-09-16
	 * @param string|null $ip [optional]
	 * @return bool
	 */
	function has($ip = null) {return dfa($this->load(), $this->ip($ip));}

	/**
	 * 2021-09-16
	 * @used-by add()
	 * @used-by has()
	 * @param string|null $v [optional]
	 */
	private function ip($v = null) {return $v ?: df_visitor_ip();}

	/**
	 * 2021-09-16
	 * @used-by add()
	 * @return array(string => int)
	 */
	private function load() {$t = time(); return array_filter(
		($j = df_cfg(self::$K)) ? df_json_decode($j) : [], function($v) use($t) {return $v < $t;}
	);}

	/**
	 * 2021-09-16
	 * @used-by add()
	 * @param array(string => int) $v
	 */
	private function save(array $v) {return df_cfg_save(self::$K, df_json_encode($v));}

	/**
	 * 2021-09-16
	 * @used-by df_ban()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}

	/**
	 * 2021-09-16
	 * @used-by cfgLoad()
	 * @used-by cfgSave()
	 * @var string
	 */
	private static $K = 'df/security/ban';
}
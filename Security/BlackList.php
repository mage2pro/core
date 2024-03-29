<?php
namespace Df\Security;
# 2021-09-16
#"Implement an ability to temporary ban visitors with a particular IP address": https://github.com/mage2pro/core/issues/159
final class BlackList {
	/**
	 * 2021-09-16
	 * @used-by df_ban()
	 */
	static function add(string $ip = ''):void {self::save([self::ip($ip) => time() + 60 * 60 * 24 * 30] + self::load());}

	/**
	 * 2021-09-16
	 * 2023-01-27
	 * «Return value of Df\Security\BlackList::has() must be of the type boolean, null returned»:
	 * https://github.com/mage2pro/core/issues/176
	 * @used-by \Df\Framework\Plugin\App\Http::aroundLaunch()
	 */
	static function has(string $ip = ''):bool {return array_key_exists(self::ip($ip), self::load());}

	/**
	 * 2021-09-16
	 * @used-by self::add()
	 * @used-by self::has()
	 */
	private static function ip(string $v = ''):string {return $v ?: df_visitor_ip();}

	/**
	 * 2021-09-16
	 * @used-by self::add()
	 * @return array(string => int)
	 */
	private static function load():array {$t = time(); /** @var int $t */ return array_filter(
		($j = df_cfg(self::$K)) ? df_json_decode($j) : [], function(int $v) use($t):bool {return $t < $v;}
	);}

	/**
	 * 2021-09-16
	 * @used-by self::add()
	 * @param array(string => int) $v
	 */
	private static function save(array $v):void {df_cfg_save_cc(self::$K, df_json_encode($v));}

	/**
	 * 2021-09-16
	 * @used-by self::cfgLoad()
	 * @used-by self::cfgSave()
	 * @var string
	 */
	private static $K = 'df/security/ban';
}
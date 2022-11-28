<?php
namespace Df\Payment\Source\API\Key;
/**
 * 2017-07-02
 * @see \Dfe\Spryng\Source\Account
 * @see \Dfe\Square\Source\Location
 */
abstract class Testable extends \Df\Payment\Source\API\Key {
	/**
	 * 2017-02-15 Первый аргумент — для тестового режима, второй — для промышленного.
	 * @used-by self::tkey()
	 * @used-by \Dfe\Spryng\Source\Account::fetch()
	 * @param mixed ...$a [optional]
	 */
	final protected function test(...$a):bool {return df_b($a, $this->_test());}

	/**
	 * 2017-02-15
	 * @used-by \Dfe\Square\Source\Location::apiKeyName()
	 * @used-by \Dfe\Spryng\Source\Account::apiKeyName()
	 */
	final protected function tkey(string $name):string {return "{$this->test('test', 'live')}$name";}

	/**
	 * 2017-03-28
	 * @used-by self::test()
	 */
	private function _test() {return dfc($this, function():bool {return df_starts_with(df_last($this->pathA()), 'test');});}
}
<?php
namespace Df\Payment\Source\API\Key;
/**
 * 2017-07-02
 * @see \Dfe\Spryng\Source\Account
 * @see \Dfe\Square\Source\Location
 */
abstract class Testable extends \Df\Payment\Source\API\Key {
	/**
	 * 2017-02-15
	 * Первый аргумент — для тестового режима, второй — для промышленного.
	 * @used-by tkey()
	 * @used-by \Dfe\Spryng\Source\Account::fetch()
	 * @param mixed[] ...$args [optional]
	 * @return bool
	 */
	final protected function test(...$args) {return df_b($args, $this->_test());}

	/**
	 * 2017-02-15
	 * @used-by \Dfe\Square\Source\Location::apiKeyName()
	 * @used-by \Dfe\Spryng\Source\Account::apiKeyName()
	 * @param string $name
	 * @return string
	 */
	final protected function tkey($name) {return "{$this->test('test', 'live')}$name";}

	/**
	 * 2017-03-28
	 * @used-by test()
	 * @return bool
	 */
	private function _test() {return dfc($this, function() {return df_starts_with(df_last(
		$this->pathA()
	), 'test');});}
}
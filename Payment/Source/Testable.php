<?php
namespace Df\Payment\Source;
use Df\Payment\Settings as S;
// 2017-02-15
/** @see \Df\Payment\Source\Testable\Api */
abstract class Testable extends \Df\Config\Source {
	/**
	 * 2017-02-15                                                             
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\Square\Source\Location::map()
	 * @return S
	 */
	protected function ss() {return dfps($this);}

	/**
	 * 2017-02-15
	 * Первый аргумент — для тестового режима, второй — для промышленного.
	 * @used-by testableKey()
	 * @usedby \Dfe\Spryng\Source\Account::fetch()
	 * @param mixed[] ...$args [optional]
	 * @return bool
	 */
	final protected function test(...$args) {return df_b($args, $this->_test());}

	/**
	 * 2017-02-15
	 * @used-by \Dfe\Square\Source\Location::map()
	 * @param string $name
	 * @return string
	 */
	final protected function tkey($name) {return "{$this->test('test', 'live')}$name";}

	/**
	 * 2017-03-28
	 * @used-by test()
	 * @return bool
	 */
	private function _test() {return dfc($this, function() {return
		df_starts_with(df_last($this->pathA()), 'test')
	;});}
}
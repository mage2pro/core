<?php
namespace Df\Config\Source;
use Df\Config\Settings as S;
/**
 * 2017-02-15
 * @see \Dfe\Square\Source\Location
 */
abstract class Testable extends \Df\Config\SourceT {
	/**
	 * 2017-02-15
	 * @used-by \Dfe\Square\Source\Location::map()
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB($this);});}

	/**
	 * 2017-02-15
	 * @used-by \Dfe\Square\Source\Location::map()
	 * @param string $name
	 * @return string
	 */
	final protected function tkey($name) {return "{$this->test('test', 'live')}$name";}

	/**
	 * 2017-02-15
	 * Первый аргумент — для тестового режима, второй — для промышленного.
	 * @used-by testableKey()
	 * @param mixed[] ...$args [optional]
	 * @return bool
	 */
	private function test(...$args) {return df_b($args, dfc($this, function($path) {return
		df_starts_with(df_last(df_explode_path($path)), 'test')
	;}, [$this['path']]));}
}
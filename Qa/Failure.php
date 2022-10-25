<?php
namespace Df\Qa;
use Df\Qa\Trace\Formatter;
/**
 * @see \Df\Qa\Failure\Error
 * @see \Df\Qa\Failure\Exception
 */
abstract class Failure {
	/**
	 * @used-by self::report()
	 * @see \Df\Qa\Failure\Error::main()
	 * @see \Df\Qa\Failure\Exception::main()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @abstract
	 * @used-by self::postface()
	 * @see \Df\Qa\Failure\Error::trace()
	 * @see \Df\Qa\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @used-by df_log_l()
	 * @used-by \Df\Qa\Failure\Error::log()
	 * @return string
	 */
	final function report() {return dfc($this, function() {return $this->sections(
		$this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @used-by self::report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @see \Df\Qa\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return Formatter::p(new Trace(array_slice($this->trace(), $this->stackLevel())));}

	/**
	 * @used-by self::report()
	 * @see \Df\Qa\Failure\Error::preface()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * @used-by self::report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @param string|string[] $items
	 * @return string
	 */
	protected function sections($items) {
		if (!is_array($items)) {
			$items = func_get_args();
		}
		static $s; if (!$s) {$s = "\n" . str_repeat('*', 36) . "\n";}; /** @var string $s */
		return implode($s, array_filter(df_trim($items)));
	}

	/**
	 * @used-by self::postface()
	 * @see \Df\Qa\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}
}
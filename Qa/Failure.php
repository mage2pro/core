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
	 */
	abstract protected function main():string;

	/**
	 * @abstract
	 * @used-by self::postface()
	 * @see \Df\Qa\Failure\Error::trace()
	 * @see \Df\Qa\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace():array;

	/**
	 * @used-by df_log_l()
	 * @used-by \Df\Qa\Failure\Error::log()
	 */
	final function report():string {return dfc($this, function() {return $this->sections(
		$this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @used-by self::report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @see \Df\Qa\Failure\Exception::postface()
	 */
	protected function postface():string {return Formatter::p(new Trace(array_slice($this->trace(), $this->stackLevel())));}

	/**
	 * @used-by self::report()
	 * @see \Df\Qa\Failure\Error::preface()
	 */
	protected function preface():string {return '';}

	/**
	 * @used-by self::report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 */
	protected function sections(string ...$a):string {
		static $s; $s = $s ? $s : "\n" . str_repeat('*', 36) . "\n"; /** @var string $s */
		return implode($s, array_filter(df_trim($a)));
	}

	/**
	 * @used-by self::postface()
	 * @see \Df\Qa\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Failure\Error::stackLevel()
	 */
	protected function stackLevel():int {return 0;}
}
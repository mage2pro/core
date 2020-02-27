<?php
namespace Df\Qa\Message;
use Df\Qa\Trace;
use Df\Qa\Trace\Formatter;
/**
 * @see \Df\Qa\Message\Failure\Error
 * @see \Df\Qa\Message\Failure\Exception
 */
abstract class Failure extends \Df\Qa\Message {
	/**
	 * @abstract
	 * @used-by frames()
	 * @see \Df\Qa\Message\Failure\Error::trace()
	 * @see \Df\Qa\Message\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @used-by df_exception_trace()
	 * @used-by postface()
	 * @return string
	 */
	final function traceS() {return Formatter::p(new Trace(
		array_slice($this->trace(), $this->stackLevel())), $this->cfg(self::P__SHOW_CODE_CONTEXT, true)
	);}

	/**
	 * @override
	 * @see \Df\Qa\Message::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @used-by \Df\Qa\Message\Failure\Exception::postface()
	 * @see \Df\Qa\Message\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return $this->traceS();}

	/**
	 * @used-by frames()
	 * @see \Df\Qa\Message\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Message\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/**
	 * @used-by traceS()
	 * @used-by df_exception_trace()
	 * @used-by df_log_e()
	 */
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}
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
	 * @used-by postface()
	 * @see \Df\Qa\Message\Failure\Error::trace()
	 * @see \Df\Qa\Message\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @override
	 * @see \Df\Qa\Message::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @used-by \Df\Qa\Message\Failure\Exception::postface()
	 * @see \Df\Qa\Message\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return Formatter::p(new Trace(
		array_slice($this->trace(), $this->stackLevel())), $this->cfg(self::P__SHOW_CODE_CONTEXT, true)
	);}

	/**
	 * @used-by postface()
	 * @see \Df\Qa\Message\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Message\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/**
	 * @used-by df_log_l()
	 * @used-by postface()
	 */
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}
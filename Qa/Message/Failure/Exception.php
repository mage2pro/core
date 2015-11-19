<?php
namespace Df\Qa\Message\Failure;
final class Exception extends \Df\Qa\Message\Failure {
	/**
	 * @override
	 * @see \Df\Qa\Message::main()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function main() {return $this->e()->getMessageRm();}

	/**
	 * @override
	 * @see \Df\Qa\Message_Failure::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function postface() {
		return $this->sections($this->sections($this->e()->comments()), parent::postface());
	}

	/**
	 * @override
	 * @see \Df\Qa\Message_Failure::stackLevel()
	 * @used-by \Df\Qa\Message_Failure::states()
	 * @return int
	 */
	protected function stackLevel() {return $this->e()->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see \Df\Qa\Message_Failure::trace()
	 * @used-by \Df\Qa\Message_Failure::states()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {return $this->e()->getTraceRm();}

	/**
	 * @used-by stackLevel()
	 * @used-by trace()
	 * @return \Df\Core\Exception
	 */
	private function e() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\Core\Exception::wrap($this[self::P__EXCEPTION]);
		}
		return $this->{__METHOD__};
	}


	const P__EXCEPTION = 'exception';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return \Df\Qa\Message\Failure\Exception
	 */
	public static function i(array $parameters = []) {return new self($parameters);}
}
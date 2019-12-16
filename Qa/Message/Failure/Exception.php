<?php
namespace Df\Qa\Message\Failure;
final class Exception extends \Df\Qa\Message\Failure {
	/**
	 * @override
	 * @see \Df\Qa\Message::main()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function main() {
		$r = $this->e()->messageL(); /** @var string $r */
		return !$this->e()->isMessageHtml() ? $r : strip_tags($r);
	}

	/**
	 * @override
	 * @see \Df\Qa\Message\Failure::postface()
	 * @used-by \Df\Qa\Message::report()
	 * @return string
	 */
	protected function postface() {return $this->sections(
		$this->sections($this->e()->comments()), parent::postface()
	);}

	/**
	 * 2016-08-20
	 * @override
	 * @see \Df\Qa\Message::reportNamePrefix()
	 * @used-by \Df\Qa\Message::reportName()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return $this->e()->reportNamePrefix();}

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
	protected function trace() {return df_ef($this->e())->getTrace();}

	/**
	 * @used-by main()
	 * @used-by stackLevel()
	 * @used-by trace()
	 * @return \Df\Core\Exception
	 */
	private function e() {return dfc($this, function() {return df_ewrap($this[self::P__EXCEPTION]);});}

	/**
	 * @used-by e()
	 * @used-by df_exception_get_trace()
	 * @used-by df_log_e()
	 * @used-by \Df\Core\Exception::getTraceAsText()
	 */
	const P__EXCEPTION = 'exception';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return \Df\Qa\Message\Failure\Exception
	 */
	static function i(array $parameters = []) {return new self($parameters);}
}
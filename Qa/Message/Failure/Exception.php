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
	protected function postface() {return $this->sections($this->sections($this->e()->comments()), parent::postface());}

	/**
	 * 2016-08-20
	 * @override
	 * @see \Df\Qa\Message::reportNamePrefix()
	 * @used-by \Df\Qa\Message::reportName()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return $this[self::P__REPORT_NAME_PREFIX] ?: $this->e()->reportNamePrefix();}

	/**
	 * @override
	 * @see \Df\Qa\Message\Failure::stackLevel()
	 * @used-by \Df\Qa\Message\Failure::frames()
	 * @return int
	 */
	protected function stackLevel() {return $this->e()->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see \Df\Qa\Message\Failure::trace()
	 * @used-by \Df\Qa\Message\Failure::frames()
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
	 * @used-by df_log_l()
	 * @used-by e()
	 */
	const P__EXCEPTION = 'exception';

	/**
	 * 2020-01-31
	 * @used-by df_log_l()
	 * @used-by reportNamePrefix()
	 */
	const P__REPORT_NAME_PREFIX = 'reportNamePrefix';

	/**
	 * @used-by df_log_l()
	 * @param array(string => mixed) $p [optional]
	 * @return self
	 */
	static function i(array $p = []) {return new self($p);}
}
<?php
namespace Df\Qa\Failure;
use Df\Core\Exception as E;
final class Exception extends \Df\Qa\Failure {
	/**
	 * @override
	 * @see \Df\Qa\Failure::main()
	 * @used-by \Df\Qa\Failure::report()
	 * @return string
	 */
	protected function main() {
		$r = $this->e()->messageL(); /** @var string $r */
		return !$this->e()->isMessageHtml() ? $r : strip_tags($r);
	}

	/**
	 * @override
	 * @see \Df\Qa\Failure::postface()
	 * @used-by \Df\Qa\Failure::report()
	 * @return string
	 */
	protected function postface() {return $this->sections($this->sections($this->e()->comments()), parent::postface());}

	/**
	 * @override
	 * @see \Df\Qa\Failure::stackLevel()
	 * @used-by \Df\Qa\Failure::postface()
	 * @return int
	 */
	protected function stackLevel() {return $this->e()->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see \Df\Qa\Failure::trace()
	 * @used-by \Df\Qa\Failure::postface()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {return df_ef($this->e())->getTrace();}

	/**
	 * @used-by main()
	 * @used-by stackLevel()
	 * @used-by trace()
	 * @return E
	 */
	private function e() {return dfc($this, function() {return df_ewrap($this->_e);});}

	/**
	 * 2021-10-04
	 * @used-by e()
	 * @used-by i()
	 * @var E
	 */
	private $_e;
	
	/**
	 * @used-by df_log_l()
	 * @param E $e
	 * @return self
	 */
	static function i(E $e) {$r = new self; $r->_e = $e; return $r;}
}
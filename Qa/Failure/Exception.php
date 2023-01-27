<?php
namespace Df\Qa\Failure;
use \Exception as E;
use Df\Core\Exception as DFE;
final class Exception extends \Df\Qa\Failure {
	/**
	 * @override
	 * @see \Df\Qa\Failure::main()
	 * @used-by \Df\Qa\Failure::report()
	 */
	protected function main():string {
		$r = $this->_e->messageD(); /** @var string $r */
		return !$this->_e->isMessageHtml() ? $r : strip_tags($r);
	}

	/**
	 * 2023-01-28
	 * I haved added `...` to overcome the error:
	 * «Argument 1 passed to Df\Qa\Failure::sections() must be of the type string, array given,
	 * called in vendor/mage2pro/core/Qa/Failure/Exception.php on line 21», https://github.com/mage2pro/core/issues/178
	 * @override
	 * @see \Df\Qa\Failure::postface()
	 * @used-by \Df\Qa\Failure::report()
	 */
	protected function postface():string {return $this->sections($this->sections(...$this->_e->comments()), parent::postface());}

	/**
	 * @override
	 * @see \Df\Qa\Failure::stackLevel()
	 * @used-by \Df\Qa\Failure::postface()
	 */
	protected function stackLevel():int {return $this->_e->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see \Df\Qa\Failure::trace()
	 * @used-by \Df\Qa\Failure::postface()
	 * @return array(array(string => string|int))
	 */
	protected function trace():array {return df_xf($this->_e)->getTrace();}

	/**
	 * 2021-10-04
	 * @used-by self::i()
	 * @used-by self::main()
	 * @used-by self::postface()
	 * @used-by self::stackLevel()
	 * @used-by self::trace()
	 * @var DFE
	 */
	private $_e;

	/** @used-by df_log_l() */
	static function i(E $e):self {$r = new self; $r->_e = DFE::wrap($e); return $r;}
}
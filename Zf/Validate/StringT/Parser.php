<?php
namespace Df\Zf\Validate\StringT;
/** @see \Df\Zf\Validate\StringT\FloatT */
abstract class Parser extends \Df\Zf\Validate {
	/** @used-by self::validator() */
	abstract protected function validatorC():string;

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @see \Df\Zf\Validate\StringT\FloatT::isValid()
	 * @param string $v
	 */
	function isValid($v):bool {
		$this->v($v);
		return $this->validator('en_US')->isValid($v) || $this->validator('ru_RU')->isValid($v);
	}

	/**
	 * @used-by self::isValid()
	 * @used-by \Df\Zf\Validate\StringT\FloatT::isValid()
	 * @return \Zend_Validate_Interface
	 */
	final protected function validator(string $l) {return dfc($this, function($l) {return df_newa(
		$this->validatorC(), \Zend_Validate_Interface::class, $l
	);}, func_get_args());}
}
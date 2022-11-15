<?php
namespace Df\Zf;
/**
 * @see \Df\Zf\Validate\Type
 * @used-by \Df\Zf\Validate\ArrayT::s()
 * @used-by \Df\Zf\Validate\IntT::s()
 * @used-by \Df\Zf\Validate\StringT::s()
 * @used-by \Df\Zf\Validate\StringT\IntT::s()
 * @used-by \Df\Zf\Validate\StringT\Iso2::s()
 * @used-by \Df\Zf\Validate\StringT\FloatT::s()
 */
abstract class Validate implements \Zend_Validate_Interface {
	/**
	 * @used-by df_float()
	 * @used-by df_int()
	 * @used-by self::getMessages()
	 * @see \Df\Zf\Validate\Type::message()
	 */
	abstract function message():string;

	/**
	 * @override
	 * @see \Zend_Validate_Interface::getMessages()
	 * @return array(string => string)
	 */
	final function getMessages():array {return [__CLASS__ => $this->message()];}

	/**
	 * @used-by \Df\Zf\Validate\ArrayT::isValid()
	 * @used-by \Df\Zf\Validate\IntT::isValid()
	 * @used-by \Df\Zf\Validate\StringT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\FloatT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\IntT::isValid()
	 * @used-by \Df\Zf\Validate\StringT\Iso2::isValid()
	 * @used-by \Df\Zf\Validate\StringT\Parser::isValid()
	 * @param mixed $v [optional]
	 * @return self|mixed
	 */
	final protected function v($v = DF_N) {return df_prop($this, $v);}
}
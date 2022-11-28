<?php
namespace Df\Zf;
/**
 * @see \Df\Zf\Validate\ArrayT
 * @see \Df\Zf\Validate\StringT
 * @see \Df\Zf\Validate\StringT\IntT
 * @see \Df\Zf\Validate\StringT\Iso2
 * @see \Df\Zf\Validate\StringT\Parser
 * @used-by \Df\Zf\Validate\ArrayT::s()
 * @used-by \Df\Zf\Validate\StringT::s()
 * @used-by \Df\Zf\Validate\StringT\IntT::s()
 * @used-by \Df\Zf\Validate\StringT\Iso2::s()
 * @used-by \Df\Zf\Validate\StringT\FloatT::s()
 */
abstract class Validate implements \Zend_Validate_Interface {
	/**
	 * @used-by self::message()
	 * @see \Df\Zf\Validate\ArrayT::expected()
	 * @see \Df\Zf\Validate\StringT::expected()
	 * @see \Df\Zf\Validate\StringT\FloatT::expected()
	 * @see \Df\Zf\Validate\StringT\IntT::expected()
	 * @see \Df\Zf\Validate\StringT\Iso2::expected()
	 */
	abstract protected function expected():string;

	/**
	 * @override
	 * @see \Zend_Validate_Interface::getMessages()
	 * @return array(string => string)
	 */
	final function getMessages():array {return [__CLASS__ => $this->message()];}

	/**
	 * @used-by df_float()
	 * @used-by df_int()
	 * @used-by \Df\Zf\Validate::getMessages()
	 */
	final function message():string {$v = $this->v(); return is_null($v)
		? "Got `NULL` instead of {$this->expected()}."
		: sprintf("Unable to recognize the value «%s» of type «%s» as {$this->expected()}.", df_string_debug($v), gettype($v))
	;}

	/**
	 * @used-by \Df\Zf\Validate\ArrayT::isValid()
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
<?php
namespace Df\Zf\Validate;
/**
 * @see \Df\Zf\Validate\ArrayT
 * @see \Df\Zf\Validate\IntT
 * @see \Df\Zf\Validate\StringT
 * @see \Df\Zf\Validate\StringT\IntT
 * @see \Df\Zf\Validate\StringT\Iso2
 * @see \Df\Zf\Validate\StringT\Parser
 */
abstract class Type extends \Df\Zf\Validate {
	/**
	 * @used-by self::_message()
	 * @see \Df\Zf\Validate\ArrayT::expected()
	 * @see \Df\Zf\Validate\IntT::expected()
	 * @see \Df\Zf\Validate\StringT::expected()
	 * @see \Df\Zf\Validate\StringT\FloatT::expected()
	 * @see \Df\Zf\Validate\StringT\IntT::expected()
	 * @see \Df\Zf\Validate\StringT\Iso2::expected()
	 * @return string
	 */
	abstract protected function expected();

	/**
	 * @override
	 * @see \Df\Zf\Validate::_message()
	 * @used-by df_float()
	 * @used-by df_int()
	 * @used-by \Df\Zf\Validate::getMessages()
	 */
	final function message():string {$v = $this->v(); return is_null($v)
		? "Got `NULL` instead of {$this->expected()}."
		: sprintf("Unable to recognize the value «%s» of type «%s» as {$this->expected()}.", df_string_debug($v), gettype($v))
	;}
}
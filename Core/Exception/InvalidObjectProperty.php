<?php
namespace Df\Core\Exception;
/**
 * @used-by Df_Core_Block_Abstract::_validateByConcreteValidator()
 * @used-by Df_Core_Block_Template::_validateByConcreteValidator()
 * @used-by Df_Core_Model::_validateByConcreteValidator()
 */
class InvalidObjectProperty extends \Df\Core\Exception {
	/**
	 * @param object $o
	 * @param string $k
	 * @param mixed $v
	 * @param \Zend_Validate_Interface $validator
	 */
	function __construct(
		$o, $k, $v, \Zend_Validate_Interface $validator) {
		parent::__construct(sprintf(
			"«%s»: значение %s недопустимо для свойства «{$k}».\nСообщение проверяющего:\n%s"
			,get_class($o)
			,df_type($v)
			,df_cc_n($validator->getMessages())
		));
	}
}
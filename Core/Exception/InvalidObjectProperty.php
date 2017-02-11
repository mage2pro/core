<?php
namespace Df\Core\Exception;
/**
 * @used-by Df_Core_Block_Abstract::_validateByConcreteValidator()
 * @used-by Df_Core_Block_Template::_validateByConcreteValidator()
 * @used-by Df_Core_Model::_validateByConcreteValidator()
 */
class InvalidObjectProperty extends \Df\Core\Exception {
	/**
	 * @param object $object
	 * @param string $propertyName
	 * @param mixed $propertyValue
	 * @param \Zend_Validate_Interface $failedValidator
	 */
	function __construct(
		$object, $propertyName, $propertyValue, \Zend_Validate_Interface $failedValidator) {
		parent::__construct(sprintf(
			"«%s»: значение %s недопустимо для свойства «%s».\nСообщение проверяющего:\n%s"
			,get_class($object)
			,df_debug_type($propertyValue)
			,$propertyName
			,df_cc_n($failedValidator->getMessages())
		));
	}
}
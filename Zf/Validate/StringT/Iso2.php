<?php
namespace Df\Zf\Validate\StringT;
class Iso2 extends \Df\Zf\Validate\Type implements \Zend_Filter_Interface {
	/**
	 * 2015-02-13
	 * Преобразовываем пустую строку в null,
	 * чтобы при наличии свойства типа
	 * $this->_prop(self::P__ISO2, DF_V_ISO2, false)
	 * валидатор не возбуждал исключительную ситуацию:
	 * «значение «» недопустимо для свойства «iso2»».
	 * Дело в том, что 3-й параметр ($isRequired) метода @see Df_Core_Model::_prop()
	 * предохраняет от исключительной ситуции при провале валидации только в том случае,
	 * если значение свойства равно null.
	 * @see Df_Core_Model::_validateByConcreteValidator()
	 * @override
	 * @param mixed $value
	 * @throws \Zend_Filter_Exception
	 * @return mixed|null
	 */
	function filter($value) {return df_empty_string($value) ? null : $value;}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @param mixed $value
	 * @return bool
	 */
	function isValid($value) {
		$this->prepareValidation($value);
		return
				is_string($value)
			&&
				(2 === mb_strlen($value))
			//&&
				//df_countries()->isIso2CodePresent($value)
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {
		return '2-буквенный код страны под стандарту ISO 3166-1';
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {
		return '2-буквенного код страны под стандарту ISO 3166-1';
	}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}
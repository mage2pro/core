<?php
namespace Df\Zf\Validate\StringT;
final class Iso2 extends \Df\Zf\Validate\Type implements \Zend_Filter_Interface {
	/**
	 * 2015-02-13
	 * Преобразовываем пустую строку в `null`, чтобы при наличии свойства типа
	 * `$this->_prop(self::P__ISO2, DF_V_ISO2, false)`
	 * валидатор не возбуждал исключительную ситуацию: «значение «» недопустимо для свойства «iso2»».
	 * Дело в том, что 3-й параметр ($isRequired) метода @see Df_Core_Model::_prop()
	 * предохраняет от исключительной ситуции при провале валидации только в том случае,
	 * если значение свойства равно null.
	 * @override
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return mixed|null
	 */
	function filter($v) {return df_es($v) ? null : $v;}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by df_check_iso2()
	 * @param mixed $v
	 * @return bool
	 */
	function isValid($v) {
		$this->prepareValidation($v);
		return is_string($v) && (2 === mb_strlen($v));
	}

	/**
	 * @override
	 * @see \Df\Zf\Validate\Type::expected()
	 * @used-by \Df\Zf\Validate\Type::_message()
	 * @return string
	 */
	protected function expected() {return 'an ISO 3166-1 alpha-2 country code';}

	/**
	 * @used-by df_check_iso2()
	 * @used-by \Df\Qa\Method::assertParamIsIso2()
	 * @used-by \Df\Qa\Method::assertValueIsIso2()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}
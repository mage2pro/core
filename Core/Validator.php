<?php
namespace Df\Core;
class Validator {
	/**
	 * 2015-04-05
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * @param mixed $value
	 * @param \Zend_Validate_Interface $validator
	 * @throws \Df\Core\Exception
	 * @return void
	 */
	public static function check($value, \Zend_Validate_Interface $validator) {
		if (!self::validate($value, $validator)) {
			df_error(
				new \Df\Core\Exception(df_cc_n($validator->getMessages())
				, df_print_params([
					'Значение' => df_debug_type($value)
					,'Проверяющий' => get_class($value)
				])
			));
		}
	}

	/**
	 * 2015-04-05
	 * @used-by \Df\Core\O::_prop()
	 * @used-by \Df\Core\O::_validate()
	 * @param object $object
	 * @param string $key
	 * @param mixed $value
	 * @param \Zend_Validate_Interface $validator
	 * @throws \Df\Core\Exception
	 * @return void
	 */
	public static function checkProperty($object, $key, $value, \Zend_Validate_Interface $validator) {
		if (!self::validate($value, $validator)) {
			df_error(new \Df\Core\Exception\InvalidObjectProperty($object, $key, $value, $validator));
		}
	}

	/**
	 * 2015-04-05
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * @used-by resolveForProperty
	 * @param \Zend_Validate_Interface|\Zend_Filter_Interface|string $validator
	 * @param bool $skipOnNull [optional]
	 * @return \Zend_Validate_Interface|\Zend_Filter_Interface
	 * @throws \Df\Core\Exception
	 */
	public static function resolve($validator, $skipOnNull = false) {
		/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $result */
		if (is_object($validator)) {
			$validator->{self::$SKIP_ON_NULL} = $skipOnNull;
			$result = $validator;
		}
		else if (is_string($validator)) {
			$result = self::byName($validator, $skipOnNull);
		}
		else {
			df_error(
				"Валидатор/фильтр имеет недопустимый тип: «%s».", gettype($validator)
			);
		}
		if (!$result instanceof \Zend_Validate_Interface && !$result instanceof \Zend_Filter_Interface) {
			df_error(
				"Валидатор/фильтр имеет недопустимый класс «%s»,"
				. ' у которого отсутствуют требуемые интерфейсы'
				.' \Zend_Validate_Interface и \Zend_Filter_Interface.'
				, get_class($result)
			);
		}
		return $result;
	}

	/**
	 * 2015-04-05
	 * @used-by Df_Core_Block_Abstract::_prop()
	 * @used-by Df_Core_Block_Template::_prop()
	 * @used-by \Df\Core\O::_prop()
	 * @param object $object
	 * @param \Zend_Validate_Interface|\Zend_Filter_Interface|string $validator
	 * @param string $key
	 * @param bool $skipOnNull [optional]
	 * @return \Zend_Validate_Interface|\Zend_Filter_Interface
	 * @throws \Df\Core\Exception
	 */
	public static function resolveForProperty($object, $validator, $key, $skipOnNull = false) {
		/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $result */
		try {
			$result = self::resolve($validator, $skipOnNull);
		}
		catch (\Df\Core\Exception $e) {
			$e->comment(df_print_params(['Класс' => get_class($object), 'Свойство' => $key]));
			throw $e;
		}
		return $result;
	}

	/**
	 * 2015-04-05
	 * Пока никем извне класса не используется, но будет.
	 * @used-by checkProperty()
	 * @param mixed $value
	 * @param \Zend_Validate_Interface $validator
	 * @throws \Df\Core\Exception
	 * @return bool
	 */
	public static function validate($value, \Zend_Validate_Interface $validator) {
		return
			is_null($value)
			&& isset($validator->{self::$SKIP_ON_NULL})
			&& $validator->{self::$SKIP_ON_NULL}
			|| $validator->isValid($value)
		;
	}

	/**
	 * 2015-04-05
	 * @used-by resolve()
	 * @param string $name
	 * @param bool $skipOnNull [optional]
	 * @return \Zend_Validate_Interface|\Zend_Filter_Interface
	 */
	private static function byName($name, $skipOnNull = false) {
		/** @var array(bool => array(string => \Zend_Validate_Interface)) */
		static $cache;
		if (!isset($cache[$skipOnNull][$name])) {
			/** @var array(string => string) $map */
			static $map;
			if (!$map) {
				/** @var string[] $entries */
				$entries = [
					DF_F_TRIM, DF_V_ARRAY, DF_V_BOOL, DF_V_FLOAT, DF_V_INT, DF_V_ISO2, DF_V_NAT
					,DF_V_NAT0, DF_V_STRING, DF_V_STRING_NE
				];
				$map = array_combine($entries, $entries);
			}
			/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $result */
			if (isset($map[$name])) {
				$result = new $map[$name];
			}
			else if (@class_exists($name) || @interface_exists($name)) {
				$result = \Df\Zf\Validate\ClassT::i($name);
			}
			else {
				df_error("Система не смогла распознать валидатор «{$name}».");
			}
			$result->{self::$SKIP_ON_NULL} = $skipOnNull;
			$cache[$skipOnNull][$name] = $result;
		}
		return $cache[$skipOnNull][$name];
	}

	/** @var string */
	private static $SKIP_ON_NULL = 'df_skip_on_null';
}
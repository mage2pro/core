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
				new \Df\Core\Exception(df_concat_n($validator->getMessages())
				, df_print_params(array(
					'Значение' => df_debug_type($value)
					,'Проверяющий' => get_class($value)
				))
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
			$e->comment(df_print_params(array('Класс' => get_class($object), 'Свойство' => $key)));
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
			static $map; if (!$map) {$map = array(
				RM_F_TRIM => '\Df\Zf\Filter\StringT\Trim'
				,RM_V_ARRAY => '\Df\Zf\Validate\ArrayT'
				,RM_V_BOOL => '\Df\Zf\Validate\BooleanT'
				,RM_V_FLOAT => '\Df\Zf\Validate\FloatT'
				,RM_V_INT => '\Df\Zf\Validate\IntT'
				,RM_V_ISO2 => '\Df\Zf\Validate\StringT\Iso2'
				,RM_V_NAT => '\Df\Zf\Validate\Nat'
				,RM_V_NAT0 => '\Df\Zf\Validate\Nat0'
				,RM_V_STRING => '\Df\Zf\Validate\StringT'
				,RM_V_STRING_NE => '\Df\Zf\Validate\StringT\NotEmpty'
			);}
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
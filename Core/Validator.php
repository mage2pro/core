<?php
namespace Df\Core;
final class Validator {
	/**
	 * 2015-04-05
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * @used-by resolveForProperty
	 * @param \Zend_Validate_Interface|\Zend_Filter_Interface|string $validator
	 * @param bool $skipOnNull [optional]
	 * @return \Zend_Validate_Interface|\Zend_Filter_Interface
	 * @throws \Df\Core\Exception
	 */
	static function resolve($validator, $skipOnNull = false) {
		/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $result */
		if (is_object($validator)) {
			$validator->{self::$SKIP_ON_NULL} = $skipOnNull;
			$result = $validator;
		}
		elseif (is_string($validator)) {
			$result = self::byName($validator, $skipOnNull);
		}
		else {
			df_error("Валидатор/фильтр имеет недопустимый тип: «%s».", gettype($validator));
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
	 * @used-by resolve()
	 * @param string $name
	 * @param bool $skipOnNull [optional]
	 * @return \Zend_Validate_Interface|\Zend_Filter_Interface
	 */
	private static function byName($name, $skipOnNull = false) {
		static $cache; /** @var array(bool => array(string => \Zend_Validate_Interface)) $cache */
		if (!isset($cache[$skipOnNull][$name])) {
			static $map; /** @var array(string => string) $map */
			if (!$map) {
				$map = dfa_combine_self(
					DF_F_TRIM, DF_V_ARRAY, DF_V_BOOL, DF_V_FLOAT, DF_V_INT, DF_V_ISO2, DF_V_NAT
					,DF_V_NAT0, DF_V_STRING, DF_V_STRING_NE
				);
			}
			/** @var \Zend_Validate_Interface|\Zend_Filter_Interface $result */
			if (isset($map[$name])) {
				$result = new $map[$name];
			}
			elseif (df_class_exists($name) || @interface_exists($name)) {
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
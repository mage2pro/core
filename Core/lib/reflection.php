<?php
/**
 * 2015-08-14
 * Обратите внимание, что @uses get_class() не ставит «\» впереди имени класса:
 * http://3v4l.org/HPF9R
	namespace A;
	class B {}
	$b = new B;
	echo get_class($b);
 * => «A\B»
 *
 * 2015-09-01
 * Обратите внимание, что @uses ltrim() корректно работает с кириллицей:
 * https://3v4l.org/rrNL9
 * echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 *
 * @used-by rm_explode_class()
 * @used-by rm_module_name()
 * @param string|object $class
 * @return string
 */
function rm_cts($class) {return is_object($class) ? get_class($class) : ltrim($class, '\\');}

/**
 * @param string|object $class
 * @return string[]
 */
function rm_explode_class($class) {return explode('\\', rm_cts($class));}

/**
 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
 * @param \Magento\Framework\DataObject|string $object
 * @return string
 */
function rm_module_name($object) {return \Df\Core\Reflection::s()->getModuleName(rm_cts($object));}
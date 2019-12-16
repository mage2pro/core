<?php
namespace Df\Core;
/**
 * Этот класс предназначен для деинициализации глобальных объектов-одиночек.
 * Опасно проводить деинициализацию глобальных объектов-одиночек в стандартном деструкторе,
 * потому что к моменту вызова деструктора для данного объекта-одиночки
 * сборщик Zend Engine мог уже уничтожить другие глобальные объекты,
 * требуемые для сохранения кэша.
 */
class GlobalSingletonDestructor {
	/**
	 * @used-by \Df\Core\Observer\ControllerFrontSendResponseAfter::execute()
	 * @uses O::_destruct()
	 */
	function process() {df_each($this->_objects, '_destruct');}

	/**
	 * @used-by df_destructable_sg()
	 * @param O $object
	 */
	function register(O $object) {$this->_objects[]= $object;}

	/**
	 * @used-by process()
	 * @used-by register()
	 * @var O[]
	 */
	private $_objects = [];

	/**
	 * @used-by df_destructable_sg()
	 * @used-by \Df\Core\Observer\ControllerFrontSendResponseAfter::execute()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}
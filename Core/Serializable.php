<?php
namespace Df\Core;
/**
 * @used-by df_serialize()
 * @used-by df_unserialize()
 * Поддержка классом данного интерфейса
 * позволяет объектам этого класса выполнять  некие действия до сериализации,
 * посое сериализации, и после десериализации.
 */
interface Serializable {
	/**
	 * В качестве параметра передаётся результат предыдущего вызова @see serializeBefore().
	 * @used-by df_serialize()
	 * @param array(string => mixed) $data
	 * @return void
	 */
	public function serializeAfter(array $data);
	/**
	 * Результат этого метода будет после сериализации передан методу @see serializeAfter().
	 * Это позволяет избежать сериализации некоторых свойств объекта
	 * (для этого надо скопировать эти свойства в контейнер,
	 * установить эти свойства в null в самом объекте,
	 * а после сериализации восстановить эти свойства в объекте из контейнера).
	 * @used-by df_serialize()
	 * @return array(string => mixed)
	 */
	public function serializeBefore();
	/**
	 * @used-by df_unserialize()
	 * @return void
	 */
	public function unserializeAfter();
}
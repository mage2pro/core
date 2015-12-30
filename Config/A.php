<?php
namespace Df\Config;
/**
 * 2015-12-30
 * Модель для @see \Df\Framework\Data\Form\Element\ArrayT
 */
class A extends \Df\Core\O implements \IteratorAggregate, \Countable {
	/**
	 * 2015-12-30
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	public function count() {return count([]);}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	public function getIterator() {return new \ArrayIterator([]);}
}
<?php
namespace Df\Config;
/**
 * 2015-12-30
 * Модель для @see \Df\Framework\Data\Form\Element\ArrayT
 * http://code.dmitry-fedyuk.com/m2/all/blob/1dca2b4fa4994b20a6a94b10b34649fb1c239189/Framework/Data/Form/Element/ArrayT.php
 */
class A extends \Df\Core\O implements \IteratorAggregate, \Countable {
	/**
	 * 2015-12-30
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	public function count() {return count($this->get());}

	/**
	 * 2015-12-30
	 * @param string|null $key [optional]
	 * @return O|array(string => O)|null
	 */
	public function get($key = null) {
		if (!isset($this->{__METHOD__})) {
			/** @var string $class */
			$class = $this[self::$P__ITEM_CLASS];
			$this->{__METHOD__} = df_index(function(O $o) {return $o->getId();}, array_map(
				function($data) use($class) {return new $class($data);}
				, array_diff_key($this[self::$P__ITEMS_A], array_flip([self::FAKE]))
			));
		}
		return is_null($key) ? $this->{__METHOD__} : dfa($this->{__METHOD__}, $key);
	}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	public function getIterator() {return new \ArrayIterator($this->get());}

	/**
	 * 2015-12-30
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ITEM_CLASS, RM_V_STRING_NE)
			->_prop(self::$P__ITEMS_A, RM_V_ARRAY)
		;
	}

	/**
	 * 2015-12-30
	 * @param string $itemClass
	 * @param mixed[] $itemsA
	 * @return $this
	 */
	public static function i($itemClass, array $itemsA) {return new self([
		self::$P__ITEM_CLASS => $itemClass, self::$P__ITEMS_A => $itemsA
	]);}

	/**
	 * 2015-12-30
	 * @used-by \Df\Framework\Data\Form\Element\ArrayT::onFormInitialized()
	 * @used-by \Df\Config\A::itemsA()
	 */
	const FAKE = 'fake';

	/** @var string */
	private static $P__ITEM_CLASS = 'item_class';
	/** @var string */
	private static $P__ITEMS_A = 'items_a';
}
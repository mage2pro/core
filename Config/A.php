<?php
namespace Df\Config;
/**
 * 2015-12-30
 * Модель для @see \Df\Framework\Form\Element\ArrayT
 * https://github.com/mage2pro/core/tree/1dca2b4fa4994b20a6a94b10b34649fb1c239189/Framework/Data/Form/Element/ArrayT.php
 */
class A extends \Df\Core\O implements \IteratorAggregate, \Countable {
	/**
	 * 2016-08-04
	 * @used-by \Df\Config\A::get()
	 * @used-by \Dfe\AllPay\ConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	function a() {return $this[self::$P__ITEMS_A];}

	/**
	 * 2015-12-30
	 * @override
	 * @see \Countable::count()
	 * @return int
	 */
	function count() {return count($this->get());}
	
	/**
	 * 2015-12-30
	 * @param string|null $key [optional]
	 * @return ArrayItem|array(string => ArrayItem)|null
	 */
	function get($key = null) {return dfak($this, function() {
		/** @var string $c */
		$c = $this[self::$P__ITEM_CLASS];
		return df_index(
			function(ArrayItem $o) {return $o->id();}
			,array_map(function($data) use($c) {return new $c($data);}, $this->a())
		);
	}, $key);}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	function getIterator() {return new \ArrayIterator($this->get());}

	/**
	 * 2015-12-30
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ITEM_CLASS, DF_V_STRING_NE)
			->_prop(self::$P__ITEMS_A, DF_V_ARRAY)
		;
	}

	/**
	 * 2015-12-30
	 * @param string $itemClass
	 * @param mixed[] $itemsA
	 * @return $this
	 */
	public static function i($itemClass, array $itemsA) {
		df_assert(!isset($itemsA[self::FAKE]));
		return new self([self::$P__ITEM_CLASS => $itemClass, self::$P__ITEMS_A => $itemsA]);
	}

	/**
	 * 2015-12-30
	 * @used-by \Df\Config\A::itemsA()
	 * @used-by \Df\Config\Backend\ArrayT::processA()
	 */
	const FAKE = 'fake';

	/** @var string */
	private static $P__ITEM_CLASS = 'item_class';
	/** @var string */
	private static $P__ITEMS_A = 'items_a';
}
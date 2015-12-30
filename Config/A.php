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
	public function count() {return count($this->items());}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * @return \Traversable
	 */
	public function getIterator() {return new \ArrayIterator($this->items());}

	/**
	 * 2015-12-30
	 * @return O[]
	 */
	private function items() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $class */
			$class = $this[self::$P__ITEM_CLASS];
			$this->{__METHOD__} = array_map(function($data) use($class) {
				return new $class($data);
			}, array_diff_key(df_nta(df_json_decode($this[self::$P__JSON])), array_flip([self::FAKE])));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-30
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ITEM_CLASS, RM_V_STRING_NE)
			->_prop(self::$P__JSON, RM_V_STRING, false)
		;
	}

	/**
	 * 2015-12-30
	 * @param string $itemClass
	 * @param string|null $json
	 * @return $this
	 */
	public static function i($itemClass, $json) {return new self([
		self::$P__ITEM_CLASS => $itemClass, self::$P__JSON => $json
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
	private static $P__JSON = 'json';
}
<?php
namespace Df\Config;
use ArrayIterator as AI;
use Df\Core\Exception as DFE;
/**
 * 2015-12-30
 * Модель для @see \Df\Framework\Form\Element\ArrayT
 * https://github.com/mage2pro/core/tree/1dca2b4fa4994b20a6a94b10b34649fb1c239189/Framework/Data/Form/Element/ArrayT.php
 */
class A extends \Df\Core\OLegacy implements \IteratorAggregate, \Countable {
	/**
	 * 2016-08-04
	 * @used-by get()
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	function a() {return $this[self::$P__ITEMS_A];}

	/**
	 * 2015-12-30
	 * @override
	 * @see \Countable::count()
	 * @return int
	 * @throws DFE
	 */
	function count() {return count($this->get());}
	
	/**
	 * 2015-12-30
	 * @used-by \Dfe\AllPay\InstallmentSales\Settings::plans()
	 * @used-by \Dfe\CurrencyFormat\Settings::get()
	 * @used-by \Dfe\Sift\Settings::pm()
	 * @used-by \Doormall\Shipping\Settings::partners()
	 * @param string|null $k [optional]
	 * @return ArrayItem|array(string => ArrayItem)|null
	 * @throws DFE
	 */
	function get($k = null) {return dfaoc($this, function() {
		$c = $this[self::$P__ITEM_CLASS]; /** @var string $c */
		return df_index(
			function(ArrayItem $o) {return $o->id();}
			,array_map(function($data) use($c) {return new $c($data);}, $this->a())
		);
	}, $k);}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * https://www.php.net/manual/iteratoraggregate.getiterator.php
	 * @return AI
	 * @throws DFE
	 */
	function getIterator() {return new AI($this->get());}

	/**
	 * 2015-12-30
	 * @override
	 * @see \Df\Core\OLegacy::_construct()
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
	 * @throws DFE
	 */
	static function i($itemClass, array $itemsA) {
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
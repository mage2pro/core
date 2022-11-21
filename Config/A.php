<?php
namespace Df\Config;
use ArrayIterator as AI;
use Df\Core\Exception as DFE;
/**
 * 2015-12-30
 * Модель для @see \Df\Framework\Form\Element\ArrayT
 * https://github.com/mage2pro/core/tree/1dca2b4fa4994b20a6a94b10b34649fb1c239189/Framework/Data/Form/Element/ArrayT.php
 */
final class A extends \Df\Core\O implements \IteratorAggregate, \Countable {
	/**
	 * 2015-12-30
	 * @override
	 * @see \Countable::count()
	 * @throws DFE
	 */
	function count():int {return count($this->get());}
	
	/**
	 * 2015-12-30
	 * @used-by self::getIterator()
	 * @used-by \Df\Config\A::count()
	 * @used-by \Dfe\AllPay\ConfigProvider::config()
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
			,array_map(function($data) use($c) {return new $c($data);}, $this[self::$P__ITEMS_A])
		);
	}, $k);}

	/**
	 * 2015-12-30
	 * @override
	 * @see \IteratorAggregate::getIterator()
	 * https://www.php.net/manual/iteratoraggregate.getiterator.php
	 * @throws DFE
	 */
	function getIterator():AI {return new AI($this->get());}

	/**
	 * 2015-12-30
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 * @used-by \Df\Config\Settings::_a()
	 * @param mixed[] $itemsA
	 * @throws DFE
	 */
	static function i(string $itemClass, array $itemsA):self {
		df_assert(!isset($itemsA[self::FAKE]));
		return new self([self::$P__ITEM_CLASS => $itemClass, self::$P__ITEMS_A => $itemsA]);
	}

	/**
	 * 2015-12-30
	 * @used-by self::i()
	 * @used-by \Df\Config\Backend\ArrayT::processA()
	 */
	const FAKE = 'fake';

	/**
	 * @used-by self::get()
	 * @used-by self::i()
	 * @var string
	 */
	private static $P__ITEM_CLASS = 'item_class';
	/**
	 * @used-by self::get()
	 * @used-by self::i()
	 * @var string
	 */
	private static $P__ITEMS_A = 'items_a';
}
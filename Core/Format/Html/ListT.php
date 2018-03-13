<?php
namespace Df\Core\Format\Html;
class ListT extends \Df\Core\O {
	/** @return string */
	private function _render() {return df_tag($this->getTag(), $this->getAttributes(), $this->renderItems());}

	/** @return array(string => string) */
	private function getAttributes() {return array_filter(['class' => $this->getCssClassForList()]);}

	/** @return array(string => string) */
	private function getAttributesForItem() {return array_filter(['class' => $this->getCssClassForItem()]);}

	/** @return string|null */
	private function getCssClassForItem() {return $this->cfg(self::$P__CSS_CLASS_FOR_ITEM);}

	/** @return string|null */
	private function getCssClassForList() {return $this->cfg(self::$P__CSS_CLASS_FOR_LIST);}

	/** @return string[] */
	private function getItems() {return $this->cfg(self::$P__ITEMS);}

	/** @return string */
	private function getTag() {return $this->isOrdered() ? 'ol' : 'ul';}

	/** @return bool */
	private function isOrdered() {return $this->cfg(self::$P__IS_ORDERED, false);}

	/**
	 * @param string $item
	 * @return string
	 */
	private function renderItem($item) {return df_tag('li', $this->getAttributesForItem(), $item);}

	/** @return string */
	private function renderItems() {return df_cc_n(array_map([$this, 'renderItem'], $this->getItems()));}

	/**
	 * @override   
	 * @see \Df\Core\O::_construct()
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__CSS_CLASS_FOR_ITEM, DF_V_STRING, false)
			->_prop(self::$P__CSS_CLASS_FOR_LIST, DF_V_STRING, false)
			->_prop(self::$P__IS_ORDERED, DF_V_BOOL, false)
			->_prop(self::$P__ITEMS, DF_V_ARRAY)
		;
	}
	/** @var string */
	private static $P__CSS_CLASS_FOR_ITEM = 'css_class_for_item';
	/** @var string */
	private static $P__CSS_CLASS_FOR_LIST = 'css_class_for_list';
	/** @var string */
	private static $P__IS_ORDERED = 'is_ordered';
	/** @var string */
	private static $P__ITEMS = 'items';

	/**
	 * @used-by df_tag_list()
	 * @param string[] $items
	 * @param bool $isOrdered [optional]
	 * @param string|null $cssClassForList [optional]
	 * @param string|null $cssClassForItem [optional]
	 * @return string
	 */
	static function render(array $items, $isOrdered = false, $cssClassForList = null, $cssClassForItem = null) {
		return (new self([
			self::$P__ITEMS => $items
			,self::$P__IS_ORDERED => $isOrdered
			,self::$P__CSS_CLASS_FOR_LIST => $cssClassForList
			,self::$P__CSS_CLASS_FOR_ITEM => $cssClassForItem
		]))->_render();
	}
}
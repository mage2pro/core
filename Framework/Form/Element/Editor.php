<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Editor as _Editor;
// 2016-01-06
class Editor extends _Editor {
	/**
	 * 2016-01-06
	 * @used-by \Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
	 * @param _Editor $editor
	 * @param string $html
	 * @return string
	 */
	final static function wrapIntoContainerSt(_Editor $editor, $html) {return $editor->_wrapIntoContainer($html);}
}

<?php
namespace Df\Framework\Form\Element;
use Magento\Framework\Data\Form\Element\Editor as _P;
# 2016-01-06
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Editor extends _P {
	/**
	 * 2016-01-06
	 * @used-by \Dfe\Markdown\Plugin\Ui\Component\Form\Element\Wysiwyg::beforePrepare()
	 */
	final static function wrapIntoContainerSt(_P $p, string $html):string {return $p->_wrapIntoContainer($html);}
}
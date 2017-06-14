<?php
namespace Df\Translation;
use Magento\Framework\View\Element\AbstractBlock;
// 2017-06-14
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Binding extends AbstractBlock {
	/**
	 * 2017-06-14
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::toHtml()
	 * @return string
	 */
	final protected function _toHtml() {return df_js(__CLASS__, 'binding');}
}
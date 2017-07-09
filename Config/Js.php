<?php
namespace Df\Config;
use Magento\Framework\View\Element\AbstractBlock as _P;
// 2017-07-09
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Js extends _P {
	/**
	 * 2017-07-09
	 * @override
	 * @see _P::_toHtml()
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::toHtml()
	 * @return string
	 */
	final protected function _toHtml() {return df_js(__CLASS__);}
}
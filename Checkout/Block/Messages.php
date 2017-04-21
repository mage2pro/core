<?php
namespace Df\Checkout\Block;
use Magento\Framework\View\Element\AbstractBlock;
/**
 * 2016-07-14
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Checkout/view/frontend/layout/checkout_index_index.xml#L13
 */
class Messages extends AbstractBlock {
	/**
	 * 2016-07-14
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::toHtml()
	 * @return string
	 */
	final protected function _toHtml() {
		/** @var array(array(string => bool|Phrase)) $m */
		$m = df_checkout_session()->getDfMessages();
		df_checkout_session()->unsDfMessages();
		return !$m ? '' : df_x_magento_init(__CLASS__, 'messages', ['messages' => $m]);
	}
}
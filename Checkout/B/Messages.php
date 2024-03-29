<?php
namespace Df\Checkout\B;
use Df\Checkout\Session as Sess;
use Magento\Framework\View\Element\AbstractBlock as _P;
/**
 * 2016-07-14
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Checkout/view/frontend/layout/checkout_index_index.xml#L13
 */
class Messages extends _P {
	/**
	 * 2016-07-14
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 */
	final protected function _toHtml():string {
		$s = Sess::s(); /** @var Sess $s */
		$m = $s->messages(); /** @var array(array(string => bool|Phrase)) $m */
		$s->messages([]);
		return !$m ? '' : df_js(__CLASS__, 'messages', ['messages' => $m]);
	}
}
<?php
namespace Df\Payment\Block;
use Df\Payment\Method as M;
use Magento\Framework\View\Element\AbstractBlock as _P;
/**
 * 2017-08-24
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \Df\Payment\Method::getFormBlockType()
 */
class Form extends _P {
	/**
	 * 2017-08-24
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @used-by \Magento\Payment\Helper\Data::getMethodFormBlock():
	 *	public function getMethodFormBlock(MethodInterface $method, LayoutInterface $layout) {
	 *		$block = $layout->createBlock($method->getFormBlockType(), $method->getCode());
	 *		$block->setMethod($method);
	 *		return $block;
	 *	}
	 * https://github.com/magento/magento2/blob/2.2.0-rc2.1/app/code/Magento/Payment/Helper/Data.php#L169-L181
	 * @param M $m
	 */
	function setMethod(M $m) {$this->_m = $m;}

	/**
	 * 2017-08-24
	 * @override
	 * @see _P::_toHtml()
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
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L642-L683
	 * @return string|null
	 */
	final protected function _toHtml() {return __('Not implemented');}

	/**
	 * 2017-08-24
	 * @used-by setMethod()
	 * @var M
	 */
	private $_m;
}
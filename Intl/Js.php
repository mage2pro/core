<?php
namespace Df\Intl;
use Magento\Framework\View\Element\AbstractBlock as _P;
# 2017-06-14
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Js extends _P {
	/**
	 * 2017-06-14
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
	final protected function _toHtml():string {return df_js(__CLASS__, '', [
		# 2023-01-28
		# «Argument 2 passed to df_intl_dic_read() must be of the type string, null given,
		# called in vendor/mage2pro/core/Intl/Js.php on line 28»: https://github.com/mage2pro/core/issues/191
		'dic' => df_intl_dic_read($this, '', 'dic')
		 # 2017-11-14
		 # "Magento 2.0.x: «Script error for: Magento_Ui/js/lib/knockout/template/renderer»"
		 # https://github.com/mage2pro/core/issues/47
		,'isMagento2.0.x' => !df_magento_version_ge('2.1.0')
	]);}
}
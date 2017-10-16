<?php
namespace Df\Payment\Block;
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Magento\Framework\View\Element\AbstractBlock as _P;
/**
 * 2017-08-24
 * 2017-10-16
 * This block is instantiated by @used-by \Df\Payment\Method::getFormBlockType():
 *		final function getFormBlockType() {return df_con_hier(
 * 			$this, \Df\Payment\Block\Multishipping::class
 * 		);}
 * https://github.com/mage2pro/core/blob/3.2.3/Payment/Method.php#L953-L979
 * @see \Dfe\Stripe\Block\Multishipping
 */
abstract class Multishipping extends _P {
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
	 * 2017-08-25
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-08-25
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @return S
	 */
	protected function s() {return $this->_m->s();}

	/**
	 * 2017-08-24
	 * @used-by m()
	 * @used-by setMethod()
	 * @var M
	 */
	private $_m;
}
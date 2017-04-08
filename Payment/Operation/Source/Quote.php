<?php
namespace Df\Payment\Operation\Source;
use Df\Payment\IMA;
use Df\Payment\Method as M;
use Magento\Quote\Api\Data\CartInterface as IQ;
use Magento\Quote\Model\Quote as Q;
// 2017-04-07
abstract class Quote extends \Df\Payment\Operation\Source {
	/**
	 * 2017-04-08
	 * @param M $m
	 * @param IQ|Q $q
	 */
	function __construct(M $m, IQ $q) {$this->_m = $m; $this->_q = $q;}

	/**
	 * 2017-04-08
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see IMA::m()
	 * @return M
	 */
	function m() {return $this->_m;}

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @used-by m()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-04-08
	 * @used-by __construct()
	 * @var M
	 */
	private $_q;
}
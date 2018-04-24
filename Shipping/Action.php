<?php
namespace Df\Shipping;
use Df\Shipping\Settings as S;
// 2018-04-24
/** @see \Doormall\Shipping\Controller\Index\Index */
abstract class Action extends \Df\Framework\Action {
	/**
	 * 2018-04-24
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Doormall\Shipping\Controller\Index\Index::execute()
	 * @return S
	 */
	protected function s() {return dfss($this->module());}
}
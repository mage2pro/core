<?php
namespace Df\Shipping;
use Df\Shipping\Settings as S;
// 2018-04-24
abstract class Action extends \Df\Framework\Action {
	/**
	 * 2018-04-24
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by needLog()
	 * @used-by \Df\Payment\CustomerReturn::execute()
	 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
	 * @return S
	 */
	protected function s() {return dfss($this->module());}
}
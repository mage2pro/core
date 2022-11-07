<?php
namespace Df\Payment;
use Df\Payment\Settings as S;
/**
 * 2016-12-25
 * @see \Df\Payment\CustomerReturn
 * @see \Df\Payment\W\Action
 */
abstract class Action extends \Df\Framework\Action {
	/**
	 * 2016-12-25
	 * @used-by \Df\Payment\CustomerReturn::execute()
	 */
	final protected function needLog():bool {return $this->s()->log();}

	/**
	 * 2016-12-25
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by self::needLog()
	 * @used-by \Df\Payment\CustomerReturn::execute()
	 * @used-by \Dfe\Stripe\Controller\CustomerReturn\Index::isSuccess()
	 */
	protected function s():S {return dfps($this->module());}
}
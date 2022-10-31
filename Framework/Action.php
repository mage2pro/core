<?php
namespace Df\Framework;
/**
 * 2017-03-19
 * @used-by \Df\Framework\Plugin\App\Request\CsrfValidator::aroundValidate()
 * @see \Df\Payment\Action
 * @see \Df\Shipping\Action
 * @see \Dfe\Portal\Controller\Index\Index
 * @see \Dfe\Sift\Controller\Index\Index
 * @see \Inkifi\Pwinty\Controller\Index\Index
 * @see \Mangoit\MediaclipHub\Controller\Index\GetPriceEndpoint
 * @see \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint
 * @see \Mineralair\Core\Controller\Modal\Index
 */
abstract class Action extends \Magento\Framework\App\Action\Action {
	/**
	 * 2017-03-19
	 * Возвращает имя модуля в формате «Dfe\Stripe».
	 * Мы должны использовать именно это имя вместо получения имени из имени текущего класса,
	 * потому что мы можем использовать virtualType,
	 * и тогда реальное имя текущего класса может не относиться к текущему модулю.
	 * @used-by \Df\Payment\Action::s()
	 * @used-by \Df\Payment\CustomerReturn::execute()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @used-by \Df\Shipping\Action::s()
	 */
	final protected function module():string {return dfc($this, function() {return df_module_name_c(
		df_request_o()->getControllerModule()
	);});}
}
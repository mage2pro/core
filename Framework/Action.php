<?php
namespace Df\Framework;
/**
 * 2017-03-19
 * @see \Df\Payment\Action
 * @see \Df\Shipping\Action
 * @see \Dfe\Portal\Controller\Index\Index
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
	 * @return string
	 */
	final protected function module() {return dfc($this, function() {return df_module_name_c(
		df_request_o()->getControllerModule()
	);});}
}
<?php
namespace Df\Framework\App;
use \Magento\Framework\App\ActionInterface;
use \Magento\Framework\App\RequestInterface;
class ActionInterfacePlugin {
	/**
	 * @param ActionInterface $subject
	 * @param RequestInterface $request
	 * @return array(RequestInterface)
	 */
	public function beforeDispatch(ActionInterface $subject, RequestInterface $request) {
		rm_state()->actionSet($subject);
		return [$request];
	}
}
<?php
namespace Df\Framework\Log\Handler;
# 2024-10-07 "Avoid logging «Can not resolve reCAPTCHA parameter» errors of bots": https://github.com/mage2pro/core/issues/443
/** @used-by \Df\Framework\Log\Dispatcher::handle() */
final class ReCaptcha extends \Df\Framework\Log\Handler {
	/**
	 * 2024-10-07
	 * @see \Magento\ReCaptchaUi\Model\CaptchaResponseResolver::resolve():
	 * 		throw new InputException(__('Can not resolve reCAPTCHA parameter.'));
	 * https://github.com/magento/security-package/blob/1.1.6-p2/ReCaptchaUi/Model/CaptchaResponseResolver.php#L25
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	#[\Override] /** @see \Df\Framework\Log\Handler::_p() */
	protected function _p():bool {return $this->r()->emsg('Can not resolve reCAPTCHA parameter');}
}
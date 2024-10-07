<?php
namespace Df\Framework\Log\Handler;
# 2024-10-07 "Avoid logging «Can not resolve reCAPTCHA parameter» errors of bots": https://github.com/mage2pro/core/issues/443
final class ReCaptcha extends \Df\Framework\Log\Handler {
	/**
	 * 2024-10-07
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	#[\Override] /** @see \Df\Framework\Log\Handler::_p() */
	protected function _p():bool {return $this->r()->msg('Can not resolve reCAPTCHA parameter');}
}
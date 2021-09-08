<?php
namespace Df\Framework\Logger\Handler;
# 2020-02-18, 2020-02-21, 2021-09-08
# "Prevent Magento from logging the «Unable to send the cookie. Maximum number of cookies would be exceeded.» message":
# https://github.com/tradefurniturecompany/site/issues/53
final class Cookie extends \Df\Framework\Logger\Handler {
	/**
	 * 2021-09-08
	 * @override
	 * @see \Df\Framework\Logger\Handler::_p()
	 * @used-by \Df\Framework\Logger\Handler::p()
	 * @return bool
	 */
	protected function _p() {return $this->msg('Unable to send the cookie. Maximum number of cookies would be exceeded.');}
}
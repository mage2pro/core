<?php
namespace Df\Framework\Plugin\App\Response;
use Magento\Framework\App\Response\HttpInterface as Sb;
use \Throwable as T;
# 2023-08-02
final class HttpInterface {
	/**
	 * 2023-08-02
	 * # "Log errors passed to \Magento\Framework\App\Bootstrap::terminate() in the developer mode":
	 * # https://github.com/mage2pro/core/issues/309
	 * @see \Magento\Framework\App\Response\HttpInterface::setBody()
	 * @see \Magento\Framework\HTTP\PhpEnvironment\Response::setBody()
	 * @param mixed $v
	 */
	function beforeSetBody(Sb $sb, $v):void {
		if ($v instanceof T) {
			df_log(null, $v);
		}
	}
}
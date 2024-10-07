<?php
namespace Df\Framework\Log\Handler;
# 2023-12-09
# "Prevent logging of «Unable to proceed: the maintenance mode is enabled»":
# https://github.com/mage2pro/core/issues/341
final class Maintenance extends \Df\Framework\Log\Handler {
	/**
	 * 2023-12-09
	 * @see \Magento\Framework\App\Bootstrap::assertMaintenance()
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {return $this->r()->msg('Unable to proceed: the maintenance mode is enabled');}
}
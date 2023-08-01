<?php
namespace Df\Framework\Log\Handler;
use Monolog\Logger as L;
# 2023-08-02
# "Prevent logging of «Broken reference»  `Monolog\Logger::INFO`-level records":
# https://github.com/mage2pro/core/issues/305
final class BrokenReference extends \Df\Framework\Log\Handler {
	/**
	 * 2023-08-02
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {return L::INFO === $this->r()->level() && $this->r()->msg('Broken reference:');}
}
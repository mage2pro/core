<?php
namespace Df\Framework\Log\Handler;
use Monolog\Logger as L;
# 2023-08-02
# "Prevent logging of
# «Broken reference: the '…' tries to reorder itself towards '…', but their parents are different: '…' and '…' respectively»":
# https://github.com/mage2pro/core/issues/305
final class BrokenReference extends \Df\Framework\Log\Handler {
	/**
	 * 2023-08-02
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {$m = $this->r()->msg(); /** @var string  $m */return
		L::INFO === $this->r()->level()
		&& df_starts_with($m, 'Broken reference:')
		&& df_contains($m, 'tries to reorder itself towards')
	;}
}
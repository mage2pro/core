<?php
namespace Df\Framework\Log\Handler;
use Monolog\Logger as L;
# 2023-08-25 "Prevent logging of «Requested path <…>.js.map is wrong»": https://github.com/mage2pro/core/issues/323
final class JsMap extends \Df\Framework\Log\Handler {
	/**
	 * 2023-08-25
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {/** @var string $m */return
		df_starts_with($m = $this->r()->msg(), 'Requested path ') && df_ends_with($m, '.js.map is wrong.')
	;}
}
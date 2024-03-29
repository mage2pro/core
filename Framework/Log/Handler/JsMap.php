<?php
namespace Df\Framework\Log\Handler;
# 2023-08-25 "Prevent logging of «Requested path <…>.js.map is wrong»": https://github.com/mage2pro/core/issues/323
final class JsMap extends \Df\Framework\Log\Handler {
	/**
	 * 2023-08-25
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {return
		df_starts_with($s = $this->r()->msg(), 'Requested path ') && df_ends_with($s, '.js.map is wrong.')
	;}
}
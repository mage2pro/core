<?php
namespace Df\API\Setup;
// 2018-01-20
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2018-01-20
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		if ($this->v('1.0.0')) {
			df_uninstall('Df_Api');
			df_uninstall('Df_API2');
		}
	}
}
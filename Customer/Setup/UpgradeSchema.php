<?php
namespace Df\Customer\Setup;
// 2016-08-21
final class UpgradeSchema extends \Df\Framework\SetupA\Schema {
	/**
	 * 2016-08-21
	 * @override
	 * @see \Df\Framework\SetupA\Schema::_process()
	 * @used-by \Df\Framework\SetupA\Schema::process()
	 * @return void
	 */
	protected function _process() {
		if ($this->v('1.7.2')) {
			$this->c()->addColumn($this->t('customer_entity'), self::$F__DF, 'text');
		}
	}

	/**
	 * 2016-08-21
	 * @var string
	 */
	private static $F__DF = 'df';
}
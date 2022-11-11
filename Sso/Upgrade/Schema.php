<?php
namespace Df\Sso\Upgrade;
/**
 * 2016-06-04
 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema
 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeSchema
 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema
 */
abstract class Schema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2016-06-04
	 * @used-by \Df\Sso\Upgrade\Schema::_process()
	 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema::fId()
	 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeSchema::fId()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema::fId()
	 */
	static function fId():string {df_abstract(__CLASS__); return '';}

	/**
	 * 2016-12-02
	 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
	 * @used-by \Df\Sso\CustomerReturn::fId()
	 * @used-by \Df\Sso\Upgrade\Data::_process()
	 * @param string|object $c
	 */
	final static function fIdC($c):string {return df_con_s(str_replace('_', '\\', df_cts($c)), 'Setup\UpgradeSchema', 'fId');}

	/**
	 * 2016-12-02
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeSchema::_process()
	 */
	protected function _process():void {
		if ($this->isInitial()) {
			/**
			 * 2016-06-04
			 * 2017-08-01 An Amazon ID can be long, e.g.: «amzn1.account.AGM6GZJB6GO42REKZDL33HG7GEJA»
			 * @see \Dfe\AmazonLogin\Setup\UpgradeSchema
			 */
			df_dbc_c(static::fId(), 'Amazon ID');
		}
	}
}
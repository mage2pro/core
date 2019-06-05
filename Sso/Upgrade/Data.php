<?php
namespace Df\Sso\Upgrade;
use Df\Customer\AddAttribute\Customer as Add;
/**
 * 2015-10-10
 * @see \Dfe\AmazonLogin\Setup\UpgradeData
 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeData
 * @see \Dfe\FacebookLogin\Setup\UpgradeData
 */
abstract class Data extends \Df\Framework\Upgrade\Data {
	/**
	 * 2016-06-05
	 * @used-by attribute()
	 * @see \Dfe\AmazonLogin\Setup\UpgradeData::labelPrefix()
	 * @see \Dfe\BlackbaudNetCommunity\Setup\UpgradeData::labelPrefix()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeData::labelPrefix()
	 * @return string
	 */
	abstract protected function labelPrefix();

	/**
	 * 2016-12-02
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 * @see \Dfe\FacebookLogin\Setup\UpgradeData::_process()
	 */
	protected function _process() {
		if ($this->isInitial()) {
			$this->attribute(Schema::fIdC($this), 'User ID');
		}
	}

	/**
	 * 2015-10-10
	 * @used-by _process()
	 * @used-by \Dfe\FacebookLogin\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $l
	 */
	final protected function attribute($name, $l) {Add::p($name, "{$this->labelPrefix()} $l");}
}
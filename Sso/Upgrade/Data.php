<?php
namespace Df\Sso\Upgrade;
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
	 * @param string $label
	 */
	final protected function attribute($name, $label) {
		/** @var int $ordering */
		static $ordering = 1000;
		df_eav_setup()->addAttribute('customer', $name, [
			'input' => 'text'
			,'label' => "{$this->labelPrefix()} $label"
			,'position' => $ordering++
			,'required' => false
			,'sort_order' => $ordering
			,'system' => false
			,'type' => 'static'
			,'visible' => false
		]);
		/** @var int $attributeId */
		$attributeId = df_first(df_fetch_col('eav_attribute', 'attribute_id', 'attribute_code', $name));
		df_conn()->insert(df_table('customer_form_attribute'), [
			'attribute_id' => $attributeId, 'form_code' => 'adminhtml_customer'
		]);
	}
}
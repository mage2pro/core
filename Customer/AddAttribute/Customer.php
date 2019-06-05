<?php
namespace Df\Customer\AddAttribute;
// 2019-06-03
final class Customer {
	/**
	 * 2019-06-03
	 * Magento does not have a separate table for customer address attributes
	 * and stores them in the same table as customer attributes: `customer_eav_attribute`.
	 * @used-by \Df\Sso\Upgrade\Data::attribute()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function p($name, $label, array $system = [], array $custom = []) {
		$pos = df_customer_att_next(); /** @var int $ordering */
		df_eav_setup()->addAttribute('customer', $name, $system + [
			'input' => 'text'
			,'label' => $label
			,'position' => $pos
			,'required' => false
			,'sort_order' => $pos
			,'system' => false
			,'type' => 'static'
			,'visible' => false
		]);
		if (dfa($custom, self::VISIBLE_IN_BACKEND, true)) {
			/** @var int $attributeId */
			$attributeId = df_first(df_fetch_col(
				'eav_attribute', 'attribute_id', 'attribute_code', $name
			));
			df_conn()->insert(df_table('customer_form_attribute'), [
				'attribute_id' => $attributeId, 'form_code' => 'adminhtml_customer'
			]);
		}
	}

	/**
	 * 2019-06-03
	 * @used-by p()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 */
	const VISIBLE_IN_BACKEND = 'visible_in_backend';
}
<?php
namespace Df\Customer\AddAttribute;
// 2019-06-03
final class Customer {
	/**
	 * 2019-06-03
	 * @used-by \Df\Sso\Upgrade\Data::attribute()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $o [optional]
	 */
	static function p($name, $label, array $o = []) {
		$ordering = 1000; /** @var int $ordering */
		df_eav_setup()->addAttribute('customer', $name, [
			'input' => 'text'
			,'label' => $label
			,'position' => $ordering
			,'required' => false
			,'sort_order' => $ordering
			,'system' => false
			,'type' => 'static'
			,'visible' => false
		]);
		if (dfa($o, self::VISIBLE_IN_BACKEND, true)) {
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
	 */
	const VISIBLE_IN_BACKEND = 'visible_in_backend';
}
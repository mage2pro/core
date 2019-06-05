<?php
namespace Df\Customer\AddAttribute;
use Magento\Customer\Model\Attribute\Backend\Data\Boolean as bBoolean;
// 2019-06-03
final class Customer {
	/**
	 * 2019-06-05
	 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Setup/CustomerSetup.php#L331-L339
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function checkbox($name, $label, array $system = [], array $custom = []) {self::p(
		'boolean', $name, $label, ['backend' => bBoolean::class] + $system, $custom
	);}

	/**
	 * 2019-06-05
	 * @used-by \Df\Sso\Upgrade\Data::attribute()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function text($name, $label, array $system = [], array $custom = []) {self::p(
		'text', $name, $label, $system, $custom
	);}

	/**
	 * 2019-06-03
	 * @used-by p()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 */
	const VISIBLE_IN_BACKEND = 'visible_in_backend';

	/**
	 * 2019-06-03
	 * Magento does not have a separate table for customer address attributes
	 * and stores them in the same table as customer attributes: `customer_eav_attribute`.
	 * @used-by checkbox()
	 * @used-by text()
	 * @param string $input
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	private static function p($input, $name, $label, array $system = [], array $custom = []) {
		$pos = df_customer_att_next(); /** @var int $ordering */
		df_eav_setup()->addAttribute('customer', $name, $system + [
			'input' => $input
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
}
<?php
namespace Df\Customer\AddAttribute;
use Magento\Customer\Api\AddressMetadataInterface as IAddressMetadata;
use Magento\Eav\Api\Data\AttributeGroupInterface as IGroup;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Eav\Model\Entity\Attribute\Set as _AS;
# 2019-06-03
final class Address {
	/**
	 * 2019-06-03
	 * @used-by \Verdepieno\Core\Setup\UpgradeData::_process()
	 * @see \Df\Customer\AddAttribute\Customer::p()
	 * @param string $name
	 * @param string $label
	 */
	static function p($name, $label):void {
		# 2019-06-03
		# Magento does not have a separate table for customer address attributes
		# and stores them in the same table as customer attributes: `customer_eav_attribute`.
		$pos = df_customer_att_pos_next(); /** @var int $pos */
		df_eav_setup()->addAttribute(IAddressMetadata::ENTITY_TYPE_ADDRESS, $name, [
			'input' => 'text'
			,'label' => $label
			,'position' => $pos
			,'required' => false
			,'sort_order' => $pos
			,'system' => false
			/**
			 * 2019-03-06
			 * `varchar` (a solution without @see \Verdepieno\Core\Setup\UpgradeSchema )
			 * does not work for me.
			 * I guess it is a bug in the Magento 2 Community core.
			 */
			,'type' => 'static'
			,'visible' => true
		]);
		$a = df_eav_config()->getAttribute(IAddressMetadata::ENTITY_TYPE_ADDRESS, $name); /** @var A $a */
        $asId = df_eav_ca()->getDefaultAttributeSetId(); /** @var int $asId */
        $as = df_new_om(_AS::class); /** @var _AS $as */		
		$a->addData([
			IGroup::ATTRIBUTE_SET_ID => $asId
			,'attribute_group_id' => $as->getDefaultGroupId($asId)
			,'used_in_forms' => [
				'adminhtml_customer_address'
				,'customer_address_edit'
				,'customer_register_address'
				,'customer_address'
			]
		]);
		$a->save();
	}
}
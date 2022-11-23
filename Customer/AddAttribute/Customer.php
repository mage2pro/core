<?php
namespace Df\Customer\AddAttribute;
use Magento\Customer\Api\Data\AttributeMetadataInterface as I; # 2019-07-06 https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Api/Data/AttributeMetadataInterface.php
use Magento\Customer\Model\Attribute\Backend\Data\Boolean as bBoolean;
# 2019-06-03
final class Customer {
	/**
	 * 2019-06-05
	 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Setup/CustomerSetup.php#L331-L339
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function checkbox(string $name, string $label, array $system = [], array $custom = []):void {self::p(
		'boolean', $name, $label, ['backend' => bBoolean::class] + $system, $custom
	);}
	
	/**
	 * 2019-06-11
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function hidden(string $name, string $label, array $system = [], array $custom = []):void {self::p(
		'hidden', $name, $label, $system, $custom
	);}	

	/**
	 * 2019-06-10
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * 1) @see \Magento\Customer\Model\AttributeMetadataConverter::createMetadataAttribute():
	 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Model/AttributeMetadataConverter.php#L64-L88
	 * 2) @see \Magento\Customer\Setup\CustomerSetup::getDefaultEntities():
	 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Setup/CustomerSetup.php#L222-L231
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function select(string $name, string $label, string $sourceC, array $system = [], array $custom = []):void {self::p(
		'select', $name, $label, $system + ['source' => $sourceC], $custom
	);}

	/**
	 * 2019-06-05
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @used-by \Df\Sso\Upgrade\Data::att()
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function text(string $name, string $label, array $system = [], array $custom = []):void {self::p(
		'text', $name, $label, $system, $custom
	);}

	/**
	 * 2019-06-11
	 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	static function textarea(string $name, string $label, array $system = [], array $custom = []):void {self::p(
		'textarea', $name, $label, $system, $custom
	);}

	/**
	 * 2019-06-03
	 * @used-by self::p()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 */
	const VISIBLE_IN_BACKEND = 'visible_in_backend';

	/**
	 * 2019-06-12
	 * @used-by self::p()
	 * @used-by \Df\Customer\Setup\UpgradeData::_process()
	 * @used-by \Df\Sso\Upgrade\Data::att()
	 */
	const VISIBLE_ON_FRONTEND = 'visible_on_frontend';

	/**
	 * 2019-06-03
	 * Magento does not have a separate table for customer address attributes
	 * and stores them in the same table as customer attributes: `customer_eav_attribute`.
	 * @used-by self::checkbox()
	 * @used-by self::select()
	 * @used-by self::text()
	 * @param string $input
	 * @param string $name
	 * @param string $label
	 * @param array(string => mixed) $system [optional]
	 * @param array(string => mixed) $custom [optional]
	 */
	private static function p($input, $name, $label, array $system = [], array $custom = []):void {
		$vBackend = dfa($custom, self::VISIBLE_IN_BACKEND, true); /** @var bool $vBackend */
		$vFrontend = dfa($custom, self::VISIBLE_ON_FRONTEND, true); /** @var bool $vFrontend */
		df_eav_setup()->addAttribute('customer', $name,
			array_fill_keys(['position', I::SORT_ORDER],
				!($pos = dfa($system, 'position')) ? df_customer_att_pos_next() :
					(is_string($pos) ? df_customer_att_pos_after($pos) : $pos)
			)
			+ $system
			+ [
				'input' => $input
				,'label' => $label
				,I::REQUIRED => false
				,I::SYSTEM => false
				,'type' => 'static'
				# 2019-06-05
				# It it is `false`,
				# then the attribute will not be shown not only in the frontend, but in the backend too.
				,I::VISIBLE => $vBackend || $vFrontend
			]
		);
		if ($vBackend || $vFrontend) {
			/** @var int $id */
			$id = (int)df_first(df_fetch_col('eav_attribute', 'attribute_id', 'attribute_code', $name));
			$t = df_table('customer_form_attribute'); /** @var string $t */
			$forms = [];
			if ($vBackend) {
				$forms[]= 'adminhtml_customer';
			}
			if ($vFrontend) {
				$forms = array_merge($forms, [
					/**
					 * 2019-06-12
					 * @see \Magento\Customer\Controller\Account\CreatePost::execute():
					 * 		$customer = $this->customerExtractor->extract(
					 * 			'customer_account_create', $this->_request
					 * 		);
					 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Controller/Account/CreatePost.php#L338
					 */
					'customer_account_create'
					/**
					 * 2019-06-13
					 * https://github.com/magento/magento2/blob/2.3.1/app/code/Magento/Customer/Controller/Account/EditPost.php#L41-L44
					 * @see \Magento\Customer\Controller\Account\EditPost::FORM_DATA_EXTRACTOR_CODE
					 */
					,'customer_account_edit'
					/**
					 * 2019-06-13
					 * As I understand `checkout_register` is not used in Magento 2 anymore:
					 * @see \Magento\Customer\Setup\Patch\Data\RemoveCheckoutRegisterAndUpdateAttributes::apply()
					 */
				]);
			}
			foreach ($forms as $f) { /** @var string $f */
				df_conn()->insert($t, ['attribute_id' => $id, 'form_code' => $f]);
			}
		}
	}
}
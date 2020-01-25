<?php
/**       
 * 2016-06-04
 * @used-by df_customer_att_is_required()
 * @param string $c
 * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
 */
function df_customer_att($c) {return df_eav_config()->getAttribute(df_eav_customer(), $c);}

/**      
 * 2016-06-04
 * @used-by \Df\Sso\Customer::dob()
 * @used-by \Df\Sso\CustomerReturn::customerData()
 * @param string $c
 * @return bool
 */
function df_customer_att_is_required($c) {return df_customer_att($c)->getIsRequired();}

/**
 * 2019-06-15
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @param string $a
 * @return int
 */
function df_customer_att_pos_after($a) {return 1 + (int)df_conn()->fetchOne(
	df_db_from(['ca' => 'customer_eav_attribute'], 'sort_order')
		->joinInner(['a' => 'eav_attribute'], 'a.attribute_id = ca.attribute_id', [])
		->where('? = a.attribute_code', $a)
);}

/**        
 * 2019-06-03 
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @return int
 */
function df_customer_att_pos_next() {return 10 + df_fetch_col_max('customer_eav_attribute', 'sort_order');}

/**
 * 2019-06-15
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @param string $a
 * @param int $pos
 */
function df_customer_att_pos_set($a, $pos) {
	df_conn()->update(df_table('customer_eav_attribute'),
		['sort_order' => $pos], ['? = attribute_id' => df_att_code2id($a)]
	);
}
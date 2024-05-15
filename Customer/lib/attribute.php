<?php
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface as IA;
/**       
 * 2016-06-04
 * 2024-05-15
 * 1) "Improve `df_customer_att()`": https://github.com/mage2pro/core/issues/371
 * 2) `print_r([2 => 'numeric', '2' => 'literal']);` => «Array ([2] => literal)»
 * https://3v4l.org/jatMt
 * @used-by df_customer_att_is_required()
 * @param string|int|IA $a
 */
function df_customer_att($a):A {return df_eav_config()->getAttribute(df_eav_customer(), $a);}

/**      
 * 2016-06-04
 * @used-by \Df\Sso\Customer::dob()
 * @used-by \Df\Sso\CustomerReturn::customerData()
 */
function df_customer_att_is_required(string $c):bool {return df_customer_att($c)->getIsRequired();}

/**
 * 2019-06-15
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 */
function df_customer_att_pos_after(string $a):int {return 1 + (int)df_conn()->fetchOne(
	df_db_from(['ca' => 'customer_eav_attribute'], 'sort_order')
		->joinInner(['a' => 'eav_attribute'], 'a.attribute_id = ca.attribute_id', [])
		->where('? = a.attribute_code', $a)
);}

/**        
 * 2019-06-03 
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 */
function df_customer_att_pos_next():int {return 10 + df_fetch_col_max('customer_eav_attribute', 'sort_order');}

/**
 * 2019-06-15
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 */
function df_customer_att_pos_set(string $a, int $pos):void {df_conn()->update(
	df_table('customer_eav_attribute'), ['sort_order' => $pos], ['? = attribute_id' => df_att_code2id($a)]
);}
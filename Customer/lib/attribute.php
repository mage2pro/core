<?php
/**        
 * 2019-06-03 
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @return int
 */
function df_customer_att_next() {return 1 + df_fetch_col_max('customer_eav_attribute', 'sort_order');}
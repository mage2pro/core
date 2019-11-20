<?php
use Magento\Sales\Model\ResourceModel\Order\Collection as C;
/**
 * 2019-11-20 
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @return C
 */
function df_order_c() {return df_new_om(C::class);}
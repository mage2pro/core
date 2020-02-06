<?php
use Closure as F;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Group as G;
use Magento\Customer\Model\GroupRegistry as Registry;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2020-02-06     
 * @used-by df_customer_group_name()
 * @param C|G|int $v
 * @return G
 * @throws NSE
 */
function df_customer_group($v) {return $v instanceof G ? $v : df_customer_group_reg()->retrieve(
	$v instanceof C ? $v->getGroupId() : $v
);}

/**
 * 2020-02-06
 * @used-by \Dfe\Sift\Observer\Customer\RegisterSuccess::execute()
 * @param C|G|int $v 
 * @param F|bool|mixed $onE [optional]
 * @return G
 * @throws NSE
 */
function df_customer_group_name($v, $onE = null) {return df_try(function() use($v) {return 
	df_customer_group($v)->getCode()
;}, $onE);}

/**
 * 2020-02-06
 * @used-by df_customer_group()
 * @return Registry
 */
function df_customer_group_reg() {return df_o(Registry::class);}
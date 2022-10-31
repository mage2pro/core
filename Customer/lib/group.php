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
 * @throws NSE
 */
function df_customer_group($v):G {return $v instanceof G ? $v : df_customer_group_reg()->retrieve(
	$v instanceof C ? $v->getGroupId() : $v
);}

/**
 * 2020-02-06
 * @used-by \Dfe\Sift\Payload\LoginOrRegister::p()
 * @param C|G|int $v 
 * @param F|bool|mixed $onE [optional]
 * @throws NSE
 */
function df_customer_group_name($v, $onE = null):G {return df_try(function() use($v) {return
	df_customer_group($v)->getCode()
;}, $onE);}

/**
 * 2020-02-06
 * @used-by df_customer_group()
 */
function df_customer_group_reg():Registry {return df_o(Registry::class);}
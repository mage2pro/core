<?php
/**
 * @param string $code
 * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
 */
function rm_customer_attribute($code) {
	return df_eav_config()->getAttribute(df_eav_customer(), $code);
}

/** @return \Magento\Customer\Api\GroupManagementInterface|\Magento\Customer\Model\GroupManagement */
function rm_customer_group_m() {return df_o('Magento\Customer\Api\GroupManagementInterface');}

/** @return bool */
function rm_customer_logged_in() {return rm_session_customer()->isLoggedIn();}

/** @return bool */
function rm_customer_logged_in_2() {
	/** @var \Magento\Framework\App\Http\Context $context */
	$context = df_o('Magento\Framework\App\Http\Context');
	return $context->getValue(Magento\Customer\Model\Context::CONTEXT_AUTH);
}

/**
 * @param \Magento\Customer\Model\Customer $customer
 * @return void
 */
function rm_customer_save(\Magento\Customer\Model\Customer $customer) {
	/** @var \Magento\Customer\Api\CustomerRepositoryInterface|\Magento\Customer\Model\ResourceModel\CustomerRepository $repository */
	$repository = df_o('Magento\Customer\Api\CustomerRepositoryInterface');
	$repository->save($customer->getDataModel());
}

/**
 * @param string $code
 * @return bool
 */
function rm_is_customer_attribute_required($code) {
	return rm_customer_attribute($code)->getIsRequired();
}

/** @return \Magento\Customer\Model\Session */
function rm_session_customer() {return df_o('Magento\Customer\Model\Session');}
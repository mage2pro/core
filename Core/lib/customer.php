<?php
/**
 * @param string $code
 * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
 */
function df_customer_attribute($code) {
	return df_eav_config()->getAttribute(df_eav_customer(), $code);
}

/** @return \Magento\Customer\Api\GroupManagementInterface|\Magento\Customer\Model\GroupManagement */
function df_customer_group_m() {return df_o(\Magento\Customer\Api\GroupManagementInterface::class);}

/**
 * 2015-11-09
 * Сегодня заметил странную ситуацию, что метод @uses \Magento\Customer\Model\Session::isLoggedIn()
 * для авторизованных посетителей стал почему-то возвращать false
 * в контексте вызова из @used-by \Dfe\Facebook\Block\Login::toHtml().
 * Также заметил, что стандартный блок авторизации в шапке страницы
 * определяет авторизованность посетителя совсем по-другому алгоритму:
 * @see \Magento\Customer\Block\Account\AuthorizationLink::isLoggedIn()
 * Вот именно этот алгоритм мы сейчас и задействуем.
 * @return bool
 */
function df_customer_logged_in() {
	return df_session_customer()->isLoggedIn() || df_customer_logged_in_2();
}

/**
 * 2015-11-09
 * Этот способ определения авторизованности посетителя
 * использует стандартный блок авторизации в шапке страницы:
 * @see \Magento\Customer\Block\Account\AuthorizationLink::isLoggedIn()
 * @return bool
 */
function df_customer_logged_in_2() {
	/** @var \Magento\Framework\App\Http\Context $context */
	$context = df_o(\Magento\Framework\App\Http\Context::class);
	return $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
}

/**
 * @param \Magento\Customer\Model\Customer $customer
 * @return void
 */
function df_customer_save(\Magento\Customer\Model\Customer $customer) {
	/** @var \Magento\Customer\Api\CustomerRepositoryInterface|\Magento\Customer\Model\ResourceModel\CustomerRepository $repository */
	$repository = df_o(\Magento\Customer\Api\CustomerRepositoryInterface::class);
	$repository->save($customer->getDataModel());
}

/**
 * @param string $code
 * @return bool
 */
function df_is_customer_attribute_required($code) {return df_customer_attribute($code)->getIsRequired();}

/** @return \Magento\Customer\Model\Session */
function df_session_customer() {return df_o(\Magento\Customer\Model\Session::class);}
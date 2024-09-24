<?php
use Df\Customer\Model\Session as DfSession;
use Magento\Customer\Api\AccountManagementInterface as IAM;
use Magento\Customer\Api\CustomerRepositoryInterface as IRep;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\AccountManagement as AM;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Model\ResourceModel\Customer as CR;
use Magento\Customer\Model\ResourceModel\CustomerRepository as Rep;
use Magento\Customer\Model\Session;

/**
 * 2016-12-04
 * @used-by df_customer_is_need_confirm()
 * @return IAM|AM
 */
function df_customer_am() {return df_o(IAM::class);}

/**
 * @used-by Df\Sso\CustomerReturn::register()
 * @return GroupManagementInterface|GroupManagement
 */
function df_customer_group_m() {return df_o(GroupManagementInterface::class);}

/**
 * 2016-04-05
 * @used-by df_customer()
 * @used-by Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by Df\Sso\CustomerReturn::mc()
 * @used-by Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 */
function df_customer_registry():CustomerRegistry {return df_o(CustomerRegistry::class);}

/**
 * 2021-05-07
 * @used-by Df\Quote\Plugin\Model\QuoteAddressValidator::doValidate()
 * @return IRep|Rep
 */
function df_customer_rep() {return df_o(IRep::class);}

/**
 * 2016-12-01
 * @used-by wolf_set()
 * @used-by Df\Sso\CustomerReturn::mc()
 * @used-by Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 */
function df_customer_resource():CR {return df_o(CR::class);}

/**
 * @used-by df_customer()
 * @used-by df_customer_id()
 * @used-by df_customer_logged_in()
 * @used-by df_customer_session_id()
 * @used-by df_session()
 * @used-by wolf_sess_get()
 * @used-by wolf_set()
 * @used-by Df\Sso\Css::isAccConfirmation()
 * @used-by Df\Sso\Css::isRegCompletion()
 * @used-by Df\Sso\CustomerReturn::_execute()
 * @used-by Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @used-by Inkifi\Mediaclip\Price::get()
 * @used-by Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @return Session|DfSession
 */
function df_customer_session() {return df_o(Session::class);}
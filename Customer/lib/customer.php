<?php
use Df\Core\Exception as DFE;
use Df\Customer\Model\Session as DfSession;
use Magento\Customer\Api\AccountManagementInterface as IAM;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\AccountManagement as AM;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order as O;

/**
 * 2016-12-01
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @return bool
 */
function df_are_customers_global() {return dfcf(function() {
	$share = df_o(Share::class); /** @var Share $share */
	return $share->isGlobalScope();
});}

/**
 * 2016-04-05
 * How to get a customer by his ID? https://mage2.pro/t/1136
 * How to get a customer by his ID with the @uses \Magento\Customer\Model\CustomerRegistry::retrieve()?
 * https://mage2.pro/t/1137
 * How to get a customer by his ID with the @see \Magento\Customer\Api\CustomerRepositoryInterface::getById()?
 * https://mage2.pro/t/1138
 * 2017-06-14 The $throw argument is not used for now.
 * @used-by df_ci_get()
 * @param string|int|DC|C|null $c [optional]
 * @param bool $throw [optional]
 * @return C|null
 * @throws NoSuchEntityException|DFE
 * 2017-02-09
 * @used-by df_sentry_m()
 */
function df_customer($c = null, $throw = false) {return df_try(function() use($c) {return
	/** @var int|string|null $id */
	/**
	 * 2016-08-22
	 * Имеется ещё метод @see \Magento\Customer\Model\Session::getCustomer()
	 * однако смущает, что он напрямую загружает объект из БД, а не пользуется репозиторием.
	 */
	!$c ? (
		df_customer_session()->isLoggedIn()
			? df_customer(df_customer_session()->getCustomerId())
			: df_error('df_customer(): the argument is null and the visitor is anonymous.')
	) : ($c instanceof C ? $c : (
		($id = is_int($c) || is_string($c) ? $c : ($c instanceof DC ? $c->getId() : null))
			? df_customer_registry()->retrieve($id)
			: df_error('df_customer(): the argument of type «%s» is unrecognizable.', df_debug_type($c))
	))
;}, $throw);}

/**
 * 2016-12-04
 * @return IAM|AM
 */
function df_customer_am() {return df_o(IAM::class);}

/**
 * @param string $code
 * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
 */
function df_customer_attribute($code) {return df_eav_config()->getAttribute(df_eav_customer(), $code);}

/**
 * 2016-08-24 By analogy with @see \Magento\Backend\Block\Dashboard\Tab\Customers\Newest::getRowUrl()
 * @see df_order_backend_url()
 * @see df_cm_backend_url()
 * @param C|int|null $c
 * @return string|null
 */
function df_customer_backend_url($c) {return !$c ? null : df_url_backend_ns('customer/index/edit', [
	'id' => df_idn($c)
]);}

/** @return GroupManagementInterface|GroupManagement */
function df_customer_group_m() {return df_o(GroupManagementInterface::class);}

/**
 * 2016-12-04
 * @param C|DC|int $c
 * @return int
 */
function df_customer_id($c) {return $c instanceof C || $c instanceof DC ? $c->getId() : $c;}

/**
 * 2016-04-05
 * @return CustomerRegistry
 */
function df_customer_registry() {return df_o(CustomerRegistry::class);}

/**
 * 2016-04-05
 * @return CustomerRepositoryInterface|CustomerRepository
 */
function df_customer_repository() {return df_o(CustomerRepositoryInterface::class);}

/**
 * 2016-12-01
 * @return CustomerResource
 */
function df_customer_resource() {return df_o(CustomerResource::class);}

/**
 * @param C $customer
 */
function df_customer_save(C $customer) {df_customer_repository()->save($customer->getDataModel());}

/**
 * @used-by \Frugue\Core\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @return Session|DfSession
 */
function df_customer_session() {return df_o(Session::class);}

/**
 * 2016-12-01
 * @return Url
 */
function df_customer_url() {return df_o(Url::class);}

/**
 * @param string $code
 * @return bool
 */
function df_is_customer_attribute_required($code) {return
	df_customer_attribute($code)->getIsRequired()
;}
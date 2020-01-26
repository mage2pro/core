<?php
use Df\Core\Exception as DFE;
use Df\Customer\Model\Session as DfSession;
use Magento\Customer\Api\AccountManagementInterface as IAM;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\AccountManagement as AM;
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Model\ResourceModel\Customer as CR;
use Magento\Customer\Model\Session;
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
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_customer()
 * @used-by df_sentry_m()
 * @used-by wolf_customer_get()
 * @used-by wolf_set()
 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Df\Payment\Block\Info::c()
 * @used-by \Df\Payment\Operation::c()
 * @used-by \Df\StripeClone\Payer::customerIdSaved()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @used-by \Stock2Shop\OrderExport\Payload::get()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string|int|DC|C|null $c [optional]
 * @param bool $throw [optional]
 * @return C|O|null|false
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
			? df_customer(df_customer_id())
			: df_error('df_customer(): the argument is null and the visitor is anonymous.')
	) : ($c instanceof C ? $c : (
		($id =
			$c instanceof O ? $c->getCustomerId() : (
				is_int($c) || is_string($c) ? $c : (
					$c instanceof DC ? $c->getId() : null)
			)
		)
			? df_customer_registry()->retrieve($id)
			: df_error('df_customer(): the argument of type «%s» is unrecognizable.', df_debug_type($c))
	))
;}, $throw);}

/**
 * 2016-12-04
 * @used-by df_customer_is_need_confirm()
 * @return IAM|AM
 */
function df_customer_am() {return df_o(IAM::class);}

/**
 * 2016-08-24 By analogy with @see \Magento\Backend\Block\Dashboard\Tab\Customers\Newest::getRowUrl()
 * @used-by \Dfe\Stripe\P\Reg::p()
 * @see df_order_backend_url()
 * @see df_cm_backend_url()
 * @param C|int|null $c
 * @return string|null
 */
function df_customer_backend_url($c) {return !$c ? null : df_url_backend_ns('customer/index/edit', [
	'id' => df_idn($c)
]);}

/**
 * @used-by \Df\Sso\CustomerReturn::register()
 * @return GroupManagementInterface|GroupManagement
 */
function df_customer_group_m() {return df_o(GroupManagementInterface::class);}

/**
 * 2016-12-04
 * @used-by df_customer()
 * @used-by df_customer_is_need_confirm()
 * @used-by \Df\Customer\Plugin\Js\CustomerId::afterGetSectionData()
 * @used-by \Dfe\Sift\API\Client::_construct()()
 * @param C|DC|int|null $c [optional]
 * @return int|null
 */
function df_customer_id($c = null) {return !$c ? df_customer_session()->getId() : (
	$c instanceof C || $c instanceof DC ? $c->getId() : $c
);}

/**
 * 2016-04-05
 * @used-by df_customer()
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 * @return CustomerRegistry
 */
function df_customer_registry() {return df_o(CustomerRegistry::class);}

/**
 * 2016-12-01
 * @used-by wolf_set()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @return CR
 */
function df_customer_resource() {return df_o(CR::class);}

/**
 * @used-by df_customer()
 * @used-by df_customer_id()
 * @used-by df_customer_logged_in()
 * @used-by df_customer_session_id()
 * @used-by wolf_sess_get()
 * @used-by wolf_set()
 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
 * @used-by \Df\Customer\Plugin\Block\Form\Register::afterGetFormData()
 * @used-by \Df\Sso\Css::isAccConfirmation()
 * @used-by \Df\Sso\Css::isRegCompletion()
 * @used-by \Df\Sso\CustomerReturn::_execute()
 * @used-by \Dfe\Sift\Session::get()
 * @used-by \Dfe\TBCBank\Init::p()
 * @used-by \Dfe\TBCBank\Init\Action::redirectParams()
 * @used-by \Frugue\Store\Plugin\Framework\App\FrontControllerInterface::aroundDispatch()
 * @used-by \Inkifi\Mediaclip\Price::get()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @return Session|DfSession
 */
function df_customer_session() {return df_o(Session::class);}

/**
 * 2020-01-25
 * 2020-01-26
 * 1) The customer session ID is regenerated (changes) via the methods:
 * 1.1) @see \Magento\Customer\Model\Session::regenerateId()
 *		public function regenerateId() {
 *			parent::regenerateId();
 *			$this->_cleanHosts();
 *			return $this;
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Customer/Model/Session.php#L564-L574
 * 1.2) @see \Magento\Framework\Session\SessionManager::regenerateId()
 *		public function regenerateId() {
 *			if (headers_sent()) {
 *				return $this;
 *			}
 *			if ($this->isSessionExists()) {
 *				session_regenerate_id();
 *				$newSessionId = session_id();
 *				$_SESSION['new_session_id'] = $newSessionId;
 *				$_SESSION['destroyed'] = time();
 *				session_commit();
 *				$oldSession = $_SESSION;
 *				session_id($newSessionId);
 *				session_start();
 *				$_SESSION = $oldSession;
 *				unset($_SESSION['destroyed']);
 *				unset($_SESSION['new_session_id']);
 *			}
 *			else {
 *				session_start();
 *			}
 *			$this->storage->init(isset($_SESSION) ? $_SESSION : []);
 *			if ($this->sessionConfig->getUseCookies()) {
 *				$this->clearSubDomainSessionCookie();
 *			}
 * 			return $this;
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/lib/internal/Magento/Framework/Session/SessionManager.php#L522-L566
 * 2) regenerateId() is called from the following methods:
 * 2.1) \Magento\Backend\Model\Auth\Session::processLogin()
 * 2.2) \Magento\Checkout\Controller\Index\Index::execute():
 *		if (!$this->isSecureRequest()) {
 *			$this->_customerSession->regenerateId();
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Controller/Index/Index.php#L40-L44
 * 2.3) \Magento\Customer\Controller\Account\CreatePost::execute()
 * 2.4) \Magento\Customer\Model\Session::setCustomerAsLoggedIn()
 * 2.5) \Magento\Customer\Model\Session::setCustomerDataAsLoggedIn()
 * 2.6) \Magento\Customer\Model\Plugin\CustomerNotification::beforeDispatch():
 *		<type name="Magento\Framework\App\Action\AbstractAction">
 *			<plugin name="customerNotification" type="Magento\Customer\Model\Plugin\CustomerNotification"/>
 *		</type>
 *		public function beforeDispatch(AbstractAction $subject, RequestInterface $request) {
 *			$customerId = $this->session->getCustomerId();
 *			if ($this->state->getAreaCode() == Area::AREA_FRONTEND && $request->isPost()
 *				&& $this->notificationStorage->isExists(
 *					NotificationStorage::UPDATE_CUSTOMER_SESSION,
 *					$customerId
 *				)
 *			) {
 *				try {
 *					$this->session->regenerateId();
 *					$customer = $this->customerRepository->getById($customerId);
 *					$this->session->setCustomerData($customer);
 *					$this->session->setCustomerGroupId($customer->getGroupId());
 *					$this->notificationStorage->remove(NotificationStorage::UPDATE_CUSTOMER_SESSION, $customer->getId());
 *				} catch (NoSuchEntityException $e) {
 *					$this->logger->error($e);
 *				}
 *			}
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Customer/Model/Plugin/CustomerNotification.php#L73-L101
 * @used-by df_is_frontend()
 * @used-by df_sentry_m()
 * @return string|null
 */
function df_customer_session_id() {return df_customer_session()->getSessionId() ?: null;}
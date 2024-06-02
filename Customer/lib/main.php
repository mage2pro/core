<?php
use Magento\Customer\Model\Config\Share;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Data\Customer as DC;

/**
 * 2016-12-01
 * @used-by \Df\Sso\CustomerReturn::mc()
 */
function df_are_customers_global():bool {return dfcf(function() {
	$share = df_o(Share::class); /** @var Share $share */
	return $share->isGlobalScope();
});}

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
 * 2016-12-04
 * @used-by df_customer()
 * @used-by df_customer_is_need_confirm()
 * @used-by \Df\Customer\Plugin\Js\CustomerId::afterGetSectionData()
 * @used-by \Dfe\Sift\API\B\Event::p()
 * @used-by vendor/inkifi/mediaclip-legacy/view/frontend/templates/savedproject.phtml
 * @param C|DC|int|null $c [optional]
 */
function df_customer_id($c = null):?int {return !$c && !df_is_backend() ? df_customer_session()->getId() : (
	/**
	 * 2024-06-02
	 * 1) https://3v4l.org/Rq0u6
	 * 2.1) @uses \Magento\Customer\Model\Customer::getId()
	 * 2.2) @uses \Magento\Customer\Model\Data\Customer::getId()
	 */
	$c instanceof C || $c instanceof DC ? $c->getId() : $c
);}

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
function df_customer_session_id() {return df_etn(df_customer_session()->getSessionId());}
<?php
namespace Df\Sso;
use Df\Customer\Model\Session as DfSession;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer as MC;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as MCR;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2016-06-04
 * @see \Dfe\FacebookLogin\Controller\Index\Index
 * @see \Dfe\AmazonLogin\Controller\Index\Index
 */
abstract class ReturnT extends _P {
	/**
	 * 2016-06-04
	 * @used-by customer()
	 * @return string
	 */
	abstract protected function customerIdFieldName();

	/**
	 * 2016-06-04
	 * @used-by execute()
	 * @return string
	 */
	abstract protected function redirectUrlKey();

	/**
	 * @override
	 * @see _P::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	public function execute() {
		try {
			/** @var Session|DfSession $session */
			$session = df_o(Session::class);
			/**
			 * 2015-10-08
			 * По аналогии с @see \Magento\Customer\Controller\Account\LoginPost::execute()
			 * https://github.com/magento/magento2/blob/1.0.0-beta4/app/code/Magento/Customer/Controller/Account/LoginPost.php#L84-L85
			 */
			$session->setCustomerDataAsLoggedIn($this->customer()->getDataModel());
			$session->regenerateId();
			/**
			 * По аналогии с @see \Magento\Customer\Model\Account\Redirect::updateLastCustomerId()
			 * Напрямую тот метод вызвать не можем, потому что он protected,
			 * а использовать весь класс @see \Magento\Customer\Model\Account\Redirect пробовал,
			 * но оказалось неудобно из-за слишком сложной процедуры перенаправлений.
			 */
			if ($session->getLastCustomerId() != $session->getId()) {
				$session->unsBeforeAuthUrl()->setLastCustomerId($session->getId());
			}
		}
		catch (\Exception $e) {
			df_message_error($e);
		}
		$this->postProcess();
		// 2016-06-05
		// @see urldecode() здесь вызывать уже не надо, проверял.
		return $this->resultRedirectFactory->create()->setUrl(
			df_request($this->redirectUrlKey()) ?: df_url()
		);
	}

	/**
	 * 2016-06-04
	 * @used-by register()
	 * @return array(string => mixed)
	 */
	protected function addressData() {return [];}

	/**
	 * 2016-06-04
	 * @see \Dfe\AmazonLogin\Customer
	 * @see \Dfe\FacebookLogin\Customer
	 * @return Customer
	 */
	protected function c() {return dfc($this, function() {
		/** @var Customer $result */
		$result = df_create(df_con_heir($this, Customer::class));
		$result->validate();
		return $result;
	});}

	/**
	 * 2016-06-04
	 * @used-by customer()
	 * @used-by register()
	 * @return array(string => mixed)
	 */
	protected function customerData() {return dfc($this, function() {return df_clean([
		'firstname' => $this->c()->nameFirst()
		,'lastname' => $this->c()->nameLast()
		,'middlename' => $this->c()->nameMiddle()
		,'dob' => $this->c()->dob()
		,'email' => $this->c()->email() ?: df_next_increment('customer_entity') . '@none.com'
		/**
			if ($customer->getForceConfirmed() || $customer->getPasswordHash() == '') {
				$customer->setConfirmation(null);
			}
			elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
				$customer->setConfirmation($customer->getRandomConfirmationKey());
			}
		 * https://github.com/magento/magento2/blob/6fa09047a6d4a1ec71494fadec5a42284ba7cc1d/app/code/Magento/Customer/Model/ResourceModel/Customer.php#L133
		 */
		,'force_confirmed' => true
		,'gender' => $this->c()->gender()
		,'password' => $this->c()->password()
		,'taxvat' => df_is_customer_attribute_required('taxvat') ? '000000000000' : ''
		,$this->customerIdFieldName() => $this->c()->id()
	]);});}

	/**
	 * 2016-06-06
	 * Перечень свойств покупателя, которые надо обновить в Magento
	 * после их изменения в сторонней системе авторизации.
	 * @used-by customer()
	 * @return string[]
	 */
	protected function customerFieldsToSync() {return [];}

	/**
	 * 2016-06-05
	 * Не всегда имеет смысл автоматически создавать адрес.
	 * В частности, для Amazon решил этого не делать,
	 * потому что автоматический адрес создаётся на основании геолокации, что не точно,
	 * а в случае с Amazon мы гарантированно можем получить точный адрес из профиля Amazon,
	 * поэтому нам нет никакого смысла забивать систему неточным автоматическим адресом.
	 * @see \Dfe\AmazonLogin\Controller\Index\Index::needCreateAddress()
	 * @used-by register()
	 * @return bool
	 */
	protected function needCreateAddress() {return true;}

	/** @return MC */
	private function customer() {return dfc($this, function() {
		/** @var MCR $resource */
		$resource = df_o(MCR::class);
		/** @var MC $result */
		$result = df_om()->create(MC::class);
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_select()->from($resource->getEntityTable(), [$resource->getEntityIdField()]);
		/**
		 * 2015-10-10
		 * 1) Полученный нами от браузера идентификатор пользователя Facebook
		 * не является глобальным: он разный для разных приложений.
		 * 2) Я так понял, что нельзя использовать одно и то же приложение Facebook
		 * сразу на нескольких доменах.
		 * 3) Из пунктов 1 и 2 следует, что нам нельзя идентифицировать пользователя Facebook
		 * по его идентификатору: ведь Magento — многодоменная система.
		 *
		 * Есть выход: token_for_business
		 * https://developers.facebook.com/docs/apps/upgrading#upgrading_v2_0_user_ids
		 * https://developers.facebook.com/docs/apps/for-business
		 * https://business.facebook.com/
		 */
		// 2016-11-21
		// Добавил возможность идентификации покупателей по email.
		// Вроде бы Discourse поступает аналогично.
		/** @var string $cond */
		$cond = df_db_quote_into("? = {$this->customerIdFieldName()}", $this->c()->id());
		$select->where(!$this->c()->email() ? $cond :
			df_db_quote_into("({$cond}) OR (? = email)", $this->c()->email())
		);
		/**
		 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
		 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L215
		 */
		if ($result->getSharingConfig()->isWebsiteScope()) {
			/**
			 * @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/CustomerRegistry.php#L104
			 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L222
			 */
			$select->where('? = website_id', df_store_m()->getStore()->getWebsiteId());
		}
		/** @var int|false $customerId */
		/**
		 * 2016-03-01
		 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
		 * https://mage2.pro/t/853
		 */
		$customerId = df_conn()->fetchOne($select);
		if (!$customerId) {
			$this->register($result);
		}
		else {
			$resource->load($result, $customerId);
			// Обновляем в нашей БД полученую от сервиса авторизации информацию о покупателе.
			$result->addData(dfa_select($this->customerData(), $this->customerFieldsToSync()));
			$result->save();
		}
		/**
		 * 2015-10-08
		 * Ядро здесь делает так:
		 * $customerModel = $this->customerFactory->create()->updateData($customer);
		 * @see \Magento\Customer\Model\AccountManagement::authenticate()
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Model/AccountManagement.php#L381
		 * Я так понимаю, ядро так делает потому, что выше там код:
		 * $customer = $this->customerRepository->get($username);
		 * и этот код необязательно возвращает объект класса @see \Magento\Customer\Model\Customer
		 * а может вернуть что-то другое, поддерживающее интерфейс
		 * @see \Magento\Customer\Api\Data\CustomerInterface
		 * @see \Magento\Customer\Api\CustomerRepositoryInterface::get()
		 */
		/**
		 * По аналогии с @see \Magento\Customer\Model\AccountManagement::authenticate()
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Model/AccountManagement.php#L382-L385
		 */
		df_dispatch('customer_customer_authenticated', ['model' => $result, 'password' => '']);
		/**
		 * 2015-10-08
		 * Не знаю, нужно ли это на самом деле.
		 * Сделал по аналогии с @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
		 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Customer/Model/CustomerRegistry.php#L133-L134
		 */
		/** @var CustomerRegistry $registry */
		$registry = df_o(CustomerRegistry::class);
		$registry->push($result);
		/**
		 * 2015-12-10
		 * Иначе новый покупатель не попадает в таблицу customer_grid_flat
		 */
		$result->reindex();
		return $result;
	});}

	/**
	 * 2016-06-06
	 * @used-by execute()
	 * @return void
	 */
	protected function postProcess() {}

	/**
	 * 2015-10-12
	 * Регистрация нового клиента.
	 * @param MC $customer
	 * @return void
	 */
	private function register(MC $customer) {
		/**
		 * 2015-10-12
		 * https://github.com/magento/magento2/issues/2087
		 * Приходится присваивать магазин в 2 шага...
		 */
		/** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $store */
		$store = df_store_m()->getStore();
		$customer->setStore($store);
		$customer->setGroupId(df_customer_group_m()->getDefaultGroup($store->getId())->getId());
		$customer->addData($this->customerData());
		$customer->save();
		/**
		 * 2016-06-05
		 * Не всегда имеет смысл автоматически создавать адрес.
		 * В частности, для Amazon решил этого не делать,
		 * потому что автоматический адрес создаётся на основании геолокации, что не точно,
		 * а в случае с Amazon мы гарантированно можем получить точный адрес из профиля Amazon,
		 * поэтому нам нет никакого смысла забивать систему неточным автоматическим адресом.
		 * @see \Dfe\AmazonLogin\Controller\Index\Index::needCreateAddress()
		 */
		if ($this->needCreateAddress()) {
			/** @var Address $address */
			$address = df_om()->create(Address::class);
			$address->setCustomer($customer);
			$address->addData(df_clean($this->addressData() + [
				'firstname' => $this->c()->nameFirst()
				,'lastname' => $this->c()->nameLast()
				,'middlename' => $this->c()->nameMiddle()
				,'city' => df_visitor()->city()
				,'country_id' => df_visitor()->iso2()
				,'is_default_billing' => 1
				,'is_default_shipping' => 1
				,'postcode' => df_visitor()->postCode() ?: (
					df_is_postcode_required(df_visitor()->iso2()) ? '000000' : null
				)
				,'region' => df_visitor()->regionName()
				,'region_id' => null
				,'save_in_address_book' => 1
				,'street' => '---'
				,'telephone' => '000000'
			]));
			$address->save();
		}
		df_dispatch('customer_register_success', [
			'account_controller' => $this, 'customer' => $customer
		]);
	}
}
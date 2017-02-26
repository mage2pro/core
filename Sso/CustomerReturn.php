<?php
namespace Df\Sso;
use Df\Customer\Model\Session as DfSession;
use Df\Sso\Upgrade\Schema;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer as MC;
use Magento\Customer\Model\ResourceModel\Customer as MCR;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2016-06-04
 * @see \Dfe\AmazonLogin\Controller\Index\Index
 * @see \Dfe\BlackbaudNetCommunity\Controller\Index\Index
 * @see \Dfe\FacebookLogin\Controller\Index\Index
 */
abstract class CustomerReturn extends _P {
	/**
	 * @override
	 * @see _P::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	function execute() {
		// 2016-06-05
		// @see urldecode() здесь вызывать уже не надо, проверял.
		/** @var string $redirectUrl */
		$redirectUrl = df_request($this->redirectUrlKey()) ?: df_url();
		/**
		 * 2016-12-02
		 * Если адрес для перенаправления покупателя передётся в адресе возврата,
		 * то адрес для перенаправления там закодирован посредством @see base64_encode()
		 * @see \Dfe\BlackbaudNetCommunity\Url::get()
		 */
		if (!df_starts_with($redirectUrl, 'http')) {
			$redirectUrl = base64_decode($redirectUrl);
		}
		try {
			/** @var Session|DfSession $s */
			$s = df_customer_session();
			if (!$this->mc()) {
				/**
				 * 2016-12-01
				 * Учётная запись покупателя отсутствует в Magento,
				 * и в то же время информации провайдера SSO недостаточно
				 * для автоматической регистрации покупателя в Magento
				 * (случай Blackbaud NetCommunity).
				 * Перенаправляем покупателя на стандартную страницу регистрации Magento,
				 * где часть полей будет уже заполнена данными от провайдера SSO,
				 * а пароль будет либо скрытым, либо необязательным полем.
				 * После регистрации свежесозданная учётная запись будет привязана
				 * к учётной записи покупателя в провайдере SSO.
				 */
				$redirectUrl = df_customer_url()->getRegisterUrl();
				$s->setDfSsoId($this->c()->id());
				$s->setDfSsoRegistrationData($this->registrationData());
				$s->setDfSsoProvider(df_module_name($this));
				/** @var Settings $settings */
				$settings = Settings::convention($this);
				df_message_success($settings->regCompletionMessage());
			}
			else {
				/**
				 * 2015-10-08
				 * По аналогии с @see \Magento\Customer\Controller\Account\LoginPost::execute()
				 * https://github.com/magento/magento2/blob/1.0.0-beta4/app/code/Magento/Customer/Controller/Account/LoginPost.php#L84-L85
				 */
				$s->setCustomerDataAsLoggedIn($this->mc()->getDataModel());
				$s->regenerateId();
				/**
				 * По аналогии с @see \Magento\Customer\Model\Account\Redirect::updateLastCustomerId()
				 * Напрямую тот метод вызвать не можем, потому что он protected,
				 * а использовать весь класс @see \Magento\Customer\Model\Account\Redirect пробовал,
				 * но оказалось неудобно из-за слишком сложной процедуры перенаправлений.
				 */
				if ($s->getLastCustomerId() != $s->getId()) {
					$s->unsBeforeAuthUrl()->setLastCustomerId($s->getId());
				}
			}
		}
		catch (\Exception $e) {
			df_message_error($e);
		}
		$this->postProcess();
		return $this->resultRedirectFactory->create()->setUrl($redirectUrl);
	}

	/**
	 * 2016-06-04
	 * @used-by register()
	 * @return array(string => mixed)
	 */
	protected function addressData() {return [];}

	/**
	 * 2016-06-04
	 * 2016-12-01
	 * @see \Dfe\AmazonLogin\Customer
	 * @see \Dfe\FacebookLogin\Customer
	 * 2017-02-26
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @final
	 * @see \Dfe\FacebookLogin\Controller\Index\Index
	 * @see \Dfe\AmazonLogin\Controller\Index\Index
	 * @return Customer
	 */
	protected function c() {return dfc($this, function() {
		/** @var Customer $result */
		$result = df_create(df_con_heir($this, Customer::class));
		$result->validate();
		return $result;
	});}

	/**
	 * 2016-12-01
	 * Если полученной от провайдера SSO информации недостаточно для регистрации покупателя в Magento
	 * (так, например, для Blackbaud NetCommunity),
	 * то это метод должен вернуть false:
	 * @see \Dfe\BlackbaudNetCommunity\Controller\Index\Index::canRegister()
	 * В этом случае покупатель-новичок не будет автоматически зарегистрирован,
	 * а вместо этого будет перенаправлен на стандартную страницу регистрации Magento,
	 * где часть полей будет уже заполнена данными от провайдера SSO,
	 * а пароль будет либо скрытым, либо необязательным полем.
	 * После регистрации свежесозданная учётная запись будет привязана
	 * к учётной записи покупателя в провайдере SSO.
	 * @used-by mc()
	 * @return bool
	 */
	protected function canRegister() {return true;}

	/**
	 * 2016-06-04
	 * @used-by mc()
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
		,$this->fId() => $this->c()->id()
	]);});}

	/**
	 * 2016-06-06
	 * Перечень свойств покупателя, которые надо обновить в Magento
	 * после их изменения в сторонней системе авторизации.
	 * @used-by mc()
	 * @return string[]
	 */
	protected function customerFieldsToSync() {return [$this->fId()];}

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

	/**
	 * 2016-12-02
	 * @used-by execute()
	 * @return array(string => mixed)
	 */
	protected function registrationData() {return [];}

	/**         
	 * @used-by execute()
	 * @return MC|null
	 * 2016-12-01
	 * Отныне метод может (и будет) возвращать null в том случае,
	 * когда учётная запись покупателя отсутствует в Magento,
	 * а метод @see canRegister() вернул false (случай Blackbaud NetCommunity).
	 */
	private function mc() {return dfc($this, function() {
		/** @var MCR $resource */
		$resource = df_customer_resource();
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_db_from($resource, $resource->getEntityIdField());
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
		$select->where(df_db_or(
			df_db_quote_into("? = {$this->fId()}", $this->c()->id())
			,!$this->c()->email() ? null : ['? = email', $this->c()->email()]
		));
		/**
		 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
		 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L215
		 */
		if (!df_are_customers_global()) {
			/**
			 * @see \Magento\Customer\Model\CustomerRegistry::retrieveByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/CustomerRegistry.php#L104
			 * @see \Magento\Customer\Model\ResourceModel\Customer::loadByEmail()
			 * https://github.com/magento/magento2/blob/2e2785cc6a78dc073a4d5bb5a88bd23161d3835c/app/code/Magento/Customer/Model/Resource/Customer.php#L222
			 */
			$select->where('? = website_id', df_store_m()->getStore()->getWebsiteId());
		}
		/**
		 * 2016-03-01
		 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
		 * https://mage2.pro/t/853
		 * @var int|false $customerId
		 */
		$customerId = df_conn()->fetchOne($select);
		/** @var MC|null $result */
		if ($result = !$customerId && !$this->canRegister() ? null : df_om()->create(MC::class)) {
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
			 *
			 * 2016-12-01
			 * Однозначно нужно.
			 */
			df_customer_registry()->push($result);
			// 2015-12-10
			// Иначе новый покупатель не попадает в таблицу «customer_grid_flat».
			$result->reindex();
		}
		return $result;
	});}

	/**
	 * 2016-06-06
	 * @used-by execute()
	 * @return void
	 */
	protected function postProcess() {}

	/**
	 * 2016-06-04
	 * @used-by execute()
	 * @return string
	 */
	protected function redirectUrlKey() {return self::REDIRECT_URL_KEY;}

	/**
	 * 2016-06-04
	 * @used-by mc()
	 * @return string
	 */
	final private function fId() {return dfc($this, function() {return Schema::fIdC($this);});}

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

	/**
	 * 2016-12-02
	 * @used-by redirectUrlKey()
	 * @used-by \Dfe\BlackbaudNetCommunity\Url::get()
	 */
	const REDIRECT_URL_KEY = 'url';
}
<?php
namespace Df\Sso;
use Df\Customer\Model\Session as DfSession;
use Df\Sso\Customer as DC;
use Df\Sso\Upgrade\Schema;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer as MC;
use Magento\Customer\Model\ResourceModel\Customer as MCR;
use Magento\Customer\Model\Session;
use Magento\Framework\DB\Select;
/**
 * 2016-06-04
 * @see \Dfe\AmazonLogin\Controller\Index\Index
 * @see \Dfe\BlackbaudNetCommunity\Controller\Index\Index
 * @see \Dfe\FacebookLogin\Controller\Index\Index
 */
abstract class CustomerReturn extends \Df\OAuth\ReturnT {
	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\OAuth\ReturnT::_execute()
	 * @used-by \Df\OAuth\ReturnT::execute()
	 */
	final protected function _execute() {
		$s = df_customer_session(); /** @var Session|DfSession $s */
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
			$this->_redirectToRegistration = true;
			$s->setDfSsoId($this->c()->id());
			$s->setDfSsoRegistrationData($this->registrationData());
			$s->setDfSsoProvider(df_module_name($this));
			$settings = dfs($this); /** @var Settings $settings */
			df_message_success($settings->regCompletionMessage());
		}
		else {
			/**
			 * 2015-10-08
			 * By analogy with @see \Magento\Customer\Controller\Account\LoginPost::execute()
			 * https://github.com/magento/magento2/blob/1.0.0-beta4/app/code/Magento/Customer/Controller/Account/LoginPost.php#L84-L85
			 */
			$s->setCustomerDataAsLoggedIn($this->mc()->getDataModel());
			$s->regenerateId();
			/**
			 * By analogy with @see \Magento\Customer\Model\Account\Redirect::updateLastCustomerId()
			 * Напрямую тот метод вызвать не можем, потому что он protected,
			 * а использовать весь класс @see \Magento\Customer\Model\Account\Redirect пробовал,
			 * но оказалось неудобно из-за слишком сложной процедуры перенаправлений.
			 */
			if ($s->getLastCustomerId() != $s->getId()) {
				$s->unsBeforeAuthUrl()->setLastCustomerId($s->getId());
			}
		}
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
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @see \Dfe\FacebookLogin\Controller\Index\Index
	 * @see \Dfe\AmazonLogin\Controller\Index\Index
	 * @return DC
	 */
	protected function c() {return dfc($this, function() {
		$result = df_new(df_con_heir($this, DC::class)); /** @var DC $result */
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
	protected function customerData() {return dfc($this, function() {$c = $this->c(); return df_clean([
		'dob' => $c->dob()
		,'email' => $c->email() ?: df_next_increment('customer_entity') . '@none.com'
		,'firstname' => $c->nameFirst()
		/**
		 *	if ($customer->getForceConfirmed() || $customer->getPasswordHash() == '') {
		 *		$customer->setConfirmation(null);
		 *	}
		 *	elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
		 *		$customer->setConfirmation($customer->getRandomConfirmationKey());
		 *	}
		 * https://github.com/magento/magento2/blob/6fa09047a6d4a1ec71494fadec5a42284ba7cc1d/app/code/Magento/Customer/Model/ResourceModel/Customer.php#L133
		 */
		,'force_confirmed' => true
		,'gender' => $c->gender()
		,'lastname' => $c->nameLast()
		,'middlename' => $c->nameMiddle()
		,'password' => $c->password()
		,'taxvat' => df_is_customer_attribute_required('taxvat') ? '000000000000' : ''
		,$this->fId() => $c->id()
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
	 * 2017-08-01
	 * @override
	 * @see \Df\OAuth\ReturnT::redirectUrl()
	 * @used-by \Df\OAuth\ReturnT::execute()
	 * @return string
	 */
	final protected function redirectUrl() {return
		!$this->_redirectToRegistration ? parent::redirectUrl() : df_customer_url()->getRegisterUrl()
	;}

	/**
	 * 2016-12-02
	 * @used-by execute()
	 * @return array(string => mixed)
	 */
	protected function registrationData() {return [];}

	/**
	 * 2015-10-08
	 * 2016-12-01
	 * Отныне метод может (и будет) возвращать null в том случае,
	 * когда учётная запись покупателя отсутствует в Magento,
	 * а метод @see canRegister() вернул false (случай Blackbaud NetCommunity).
	 * @used-by _execute()
	 * @return MC|null
	 */
	private function mc() {return dfc($this, function() {
		$resource = df_customer_resource(); /** @var MCR $resource */
		$c = $this->c(); /** @var DC $c */
		$select = df_db_from($resource, $resource->getEntityIdField()); /** @var Select $select */
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
			df_db_quote_into("? = {$this->fId()}", $c->id()), !$c->email() ? null : ['? = email', $c->email()]
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
		if ($result = !$customerId && !$this->canRegister() ? null : df_new_om(MC::class)) {
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
			 * By analogy with @see \Magento\Customer\Model\AccountManagement::authenticate()
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
	 * 2016-06-04
	 * @used-by mc()
	 * @return string
	 */
	final private function fId() {return dfc($this, function() {return Schema::fIdC($this);});}

	/**
	 * 2015-10-12
	 * Регистрация нового покупателя.
	 * @used-by mc()
	 * @param MC $mc
	 */
	private function register(MC $mc) {
		// 2015-10-12
		// https://github.com/magento/magento2/issues/2087
		// Приходится присваивать магазин в 2 шага...
		/** @var \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $store */
		$store = df_store_m()->getStore();
		$mc->setStore($store);
		$mc->setGroupId(df_customer_group_m()->getDefaultGroup($store->getId())->getId());
		$mc->addData($this->customerData());
		$mc->save();
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
			$a = df_new_om(Address::class); /** @var Address $a */
			$a->setCustomer($mc);
			$v = df_visitor(); /** @var \Df\Core\Visitor $v */
			$c = $this->c(); /** @var DC $c */
			$a->addData(df_clean($this->addressData() + [
				// 2017-04-07
				// Сервис геолокации может отказать нам в данных,
				// но мы не можем передавать в ядро пустое значение:
				// иначе будет сбой: «"City" is a required value».
				'city' => $v->city() ?: 'Unknown'
				// 2017-04-07
				// Сервис геолокации может отказать нам в данных,
				// но мы не можем передавать в ядро пустое значение:
				// иначе будет сбой: «"Country" is a required value».
				,'country_id' => $v->iso2() ?: 'US'
				,'firstname' => $c->nameFirst()
				,'is_default_billing' => 1
				,'is_default_shipping' => 1
				,'lastname' => $c->nameLast()
				,'middlename' => $c->nameMiddle()
				,'postcode' => $v->postCode() ?: (df_is_postcode_required($v->iso2()) ? '000000' : null)
				,'region' => $v->regionName()
				,'region_id' => null
				,'save_in_address_book' => 1
				,'street' => '---'
				,'telephone' => '000000'
			]));
			$a->save();
		}
		df_dispatch('customer_register_success', ['account_controller' => $this, 'customer' => $mc]);
	}

	/**
	 * 2017-08-01
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
	 * @used-by _execute()
	 * @used-by redirectUrl()
	 * @var bool
	 */
	private $_redirectToRegistration = false;
}
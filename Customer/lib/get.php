<?php
use Df\Core\Exception as DFE;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order as O;

/**
 * 2016-04-05
 * How to get a customer by his ID? https://mage2.pro/t/1136
 * How to get a customer by his ID with the @uses \Magento\Customer\Model\CustomerRegistry::retrieve()?
 * https://mage2.pro/t/1137
 * How to get a customer by his ID with the @see \Magento\Customer\Api\CustomerRepositoryInterface::getById()?
 * https://mage2.pro/t/1138
 * 2017-06-14 The $onE argument is not used for now.
 * @used-by df_ci_get()
 * @used-by df_ci_get()
 * @used-by df_ci_save()
 * @used-by df_customer()
 * @used-by df_customer_id()
 * @used-by df_sentry_m()
 * @used-by wolf_customer_get()
 * @used-by wolf_set()
 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Df\Payment\Block\Info::c()
 * @used-by \Df\Payment\Operation::c()
 * @used-by \Df\StripeClone\Payer::customerIdSaved()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Dfe\Sift\Observer\Customer\RegisterSuccess::execute()
 * @used-by \Inkifi\Pwinty\API\B\Order\Create::p()
 * @used-by \KingPalm\B2B\Observer\AdminhtmlCustomerPrepareSave::execute()
 * @used-by \Stock2Shop\OrderExport\Payload::get()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string|int|DC|C|null $v [optional]
 * @param Closure|bool|mixed $onE [optional]
 * @throws NoSuchEntityException|DFE
 */
function df_customer($v = null, $onE = null):?C {return df_try(function() use($v):?C {return
	/** @var int|string|null $id */
	/**
	 * 2016-08-22
	 * I do not use @see \Magento\Customer\Model\Session::getCustomer()
	 * because it does not use the customers repository, and loads a customer directly from the database.
	 */
	!$v ? (
		df_customer_session()->isLoggedIn()
			? df_customer(df_customer_id())
			: df_error('df_customer(): the argument is `null` and the visitor is anonymous.')
	) : ($v instanceof C ? $v : (
		($id =
			$v instanceof O ? $v->getCustomerId() : (
				is_int($v) || is_string($v) ? $v : ($v instanceof DC ? $v->getId() : null)
			)
		)
			# 2024-06-02 "Improve `df_customer()`": https://github.com/mage2pro/core/issues/400
			? (df_is_email($id)
				? df_customer_registry()->retrieveByEmail($id)
				: df_customer_registry()->retrieve($id)
			)
			# 2024-05-20
			# "Provide an ability to specify a context for a `Df\Core\Exception` instance":
			# https://github.com/mage2pro/core/issues/375
			: df_error("Unable to detect the customer's ID", ['v' => $v])
	))
;}, $onE);}
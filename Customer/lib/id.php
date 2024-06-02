<?php
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Data\Customer as DC;

/**
 * 2016-12-04
 * 2024-06-02 "Improve `df_customer_id()`": https://github.com/mage2pro/core/issues/402
 * @used-by df_customer()
 * @used-by df_customer_is_need_confirm()
 * @used-by df_subscriber()
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
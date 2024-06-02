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
 * @param C|DC|int|null $v [optional]
 */
function df_customer_id($v = null, $onE = null):?int {return df_try(function() use($v, $onE):?int {return
	df_customer($v, $onE)->getId()
;}, $onE);}
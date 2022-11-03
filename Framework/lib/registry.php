<?php
use Magento\Framework\Registry as R;

/**
 * 2015-10-31
 * @used-by \MageKey\AdcPopup\Observer\QuoteAddAfter::execute()		tradefurniturecompany.co.uk
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string $k
 * @param mixed $v
 */
function df_register($k, $v):void {df_registry_o()->register($k, $v);}

/**
 * 2015-10-31
 * @used-by df_product_current()
 * @used-by \Dfe\Logo\Frontend::_toHtml()
 * @used-by \MageKey\AdcPopup\Observer\QuoteAddAfter::execute()		tradefurniturecompany.co.uk
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @param string $k
 * @return mixed|null
 */
function df_registry($k) {return df_registry_o()->registry($k);}

/**
 * 2015-11-02 https://mage2.pro/t/95
 * @used-by df_register()
 * @used-by df_registry()
 * @used-by df_unregister()
 */
function df_registry_o():R {return df_o(R::class);}

/**
 * 2020-03-01
 * @used-by \MageKey\AdcPopup\Observer\QuoteAddAfter::execute()		tradefurniturecompany.co.uk
 * @param string $k
 */
function df_unregister($k):void {df_registry_o()->unregister($k);}
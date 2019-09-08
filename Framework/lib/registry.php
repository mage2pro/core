<?php
use Magento\Framework\Registry as R;

/**
 * 2015-10-31
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @param string $k
 * @param mixed $v
 */
function df_register($k, $v) {df_registry_o()->register($k, $v);}

/**
 * 2015-10-31
 * @used-by df_product_current()
 * @used-by \Dfe\Logo\Frontend::_toHtml()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 * @param string $key
 * @return mixed|null
 */
function df_registry($k) {return df_registry_o()->registry($k);}

/**
 * 2015-11-02 https://mage2.pro/t/95
 * @used-by df_register()
 * @used-by df_registry()
 * @return R
 */
function df_registry_o() {return df_o(R::class);}
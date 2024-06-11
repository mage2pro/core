<?php
use Magento\Framework\View\Page\Config as C;
use Magento\Framework\View\Result\PageFactory as F;

/**
 * 2015-10-05
 * @used-by df_body_class()
 * @used-by df_metadata()
 * @used-by df_page_title()
 * @used-by \Df\Sso\Button::_prepareLayout()
 * @used-by \Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 */
function df_page_config():C {return df_o(C::class);}

/**
 * 2024-06-11
 * @used-by df_page_result()
 */
function df_page_factory():F {return df_o(F::class);}
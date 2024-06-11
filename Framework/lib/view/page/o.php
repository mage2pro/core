<?php
use Magento\Framework\View\Page\Config;

/**
 * 2015-10-05
 * @used-by df_metadata()
 * @used-by df_page_title()
 * @used-by \CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/3)
 * @used-by \Df\Sso\Button::_prepareLayout()
 * @used-by \Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 */
function df_page_config():Config {return df_o(Config::class);}
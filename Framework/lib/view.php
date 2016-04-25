<?php
use Magento\Framework\Config\View as ConfigView;
use Magento\Framework\View\Config as ViewConfig;
use Magento\Framework\View\ConfigInterface as ViewConfigI;
/**
 * 2016-04-23
 * By analogy with @see \Magento\Catalog\Helper\Image::getConfigView()
 * https://github.com/magento/magento2/blob/958164/app/code/Magento/Catalog/Helper/Image.php#L781-L792
 * @return ConfigView
 */
function df_view_config() {
	/** @var ViewConfig|ViewConfigI $o */
	$o = df_o(ViewConfigI::class);
	return $o->getViewConfig();
}



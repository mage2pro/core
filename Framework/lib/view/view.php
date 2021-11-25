<?php
use Magento\Framework\Config\View as ConfigView;
use Magento\Framework\View\Config as ViewConfig;
use Magento\Framework\View\ConfigInterface as ViewConfigI;
/**
 * 2016-04-23
 * By analogy with @see \Magento\Catalog\Helper\Image::getConfigView()
 * https://github.com/magento/magento2/blob/958164/app/code/Magento/Catalog/Helper/Image.php#L781-L792
 * @used-by df_product_image_url()
 * @return ConfigView
 */
function df_view_config() {
	/**
	 * 2021-11-25
	 * It fixes the «Area code is not set» error in @see \Magento\Framework\App\State::getAreaCode()
	 * when the area code is really not set (e.g., for console commands like `bin/magento tfc:google-shopping:1`).
	 */
	df_area_code_set_d();
	$o = df_o(ViewConfigI::class); /** @var ViewConfig|ViewConfigI $o */
	return $o->getViewConfig();
}



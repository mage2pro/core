<?php
use Magento\Catalog\Helper\Image as ImageH;
# 2018-07-16 This class is present in Magento 2.0.0:
# https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product/Media/Config.php
use Magento\Catalog\Model\Product\Media\Config as MC;

/**
 * 2016-04-23
 * @used-by df_product_image_url()
 * @used-by app/design/frontend/MageBig/martfury/layout01/MageBig_QuickView/templates/product/view/gallery.phtml (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by app/design/frontend/MageBig/martfury/layout01/MageBig_WidgetPlus/templates/widget/layout/view/gallery.phtml (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by app/design/frontend/MageBig/martfury/layout01/Magento_Catalog/templates/product/view/gallery.phtml:25 (innomuebles.com, https://github.com/innomuebles/m2/issues/7)
 * @used-by \Justuno\M2\Catalog\Images::p()
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 */
function df_catalog_image_h():ImageH {return df_o(ImageH::class);}

/**
 * 2018-07-16
 * @used-by \SayItWithAGift\Options\Frontend::_toHtml()
 */
function df_product_mc():MC {return df_o(MC::class);}
<?php
use Magento\Swatches\Helper\Data as SwatchesH;
/**
 * 2019-08-21
 * @used-by \Dfe\Color\Image::palette()
 * @used-by \Dfe\Color\Plugin\Catalog\Model\ResourceModel\Eav\Attribute::beforeBeforeSave()
 */
function df_swatches_h():SwatchesH {return df_o(SwatchesH::class);}
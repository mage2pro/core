<?php
use Magento\Cms\Model\BlockRepository as BR;
use Magento\Cms\Model\ResourceModel\Block\Collection as C;

/**
 * 2018-05-21
 * @used-by df_cms_block_get()
 */
function df_cms_block_r():BR {return df_o(BR::class);}

/**
 * 2018-05-21
 * @used-by Df\Config\Source\Block::map()
 */
function df_cms_blocks():C {return df_new_om(C::class);}
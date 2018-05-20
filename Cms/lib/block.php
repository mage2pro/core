<?php
use Magento\Cms\Model\ResourceModel\Block\Collection as C;
/**
 * 2018-05-21
 * @used-by \Df\Config\Source\Block::map()
 * @return C
 */
function df_cms_blocks() {return df_new_om(C::class);}
<?php
use Magento\Cms\Model\Block as B;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2018-05-21
 * 2024-03-13
 * 1) @see df_block_output()
 * 2) $id can be a numeric ID or a literal ID:
 * @see \Magento\Cms\Model\ResourceModel\Block::load()
 * @see \Magento\Cms\Model\ResourceModel\Block::getBlockId()
 * 2024-05-06 "Improve `df_cms_block*` functions": https://github.com/mage2pro/core/issues/365
 * @used-by \AlbumEnvy\Popup\Settings::content()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/bottom.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/top.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/home.phtml (https://github.com/cabinetsbay/site/issues/146)
 * @param int|string $id
 * @throws NSE
 */
function df_cms_block($id):string {return
	($b = df_cms_block_get($id)) && $b->isActive() ? df_cms_filter_block($b->getContent()) : '';
}
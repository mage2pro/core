<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2018-05-21
 * 2024-03-13
 * 1) @see df_block_output()
 * 2) $id can be a numeric ID or a literal ID:
 * @see \Magento\Cms\Model\ResourceModel\Block::load()
 * @see \Magento\Cms\Model\ResourceModel\Block::getBlockId()
 * @used-by \AlbumEnvy\Popup\Settings::content()
 * @used-by app/design/frontend/Cabinetsbay/cabinetsbay_default/Magento_Theme/templates/homepage.phtml (https://github.com/cabinetsbay/site/issues/146)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/l2/bottom.phtml (https://github.com/cabinetsbay/site/issues/112)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/l2/top.phtml (https://github.com/cabinetsbay/site/issues/112)
 * @param int|string $id
 * @param Closure|bool|string $onE [optional]
 * @throws NSE
 */
function df_cms_block_content($id, $onE = ''):string {return df_try(function() use($id):string {return
	df_cms_block($id)->getContent()
;}, $onE);}
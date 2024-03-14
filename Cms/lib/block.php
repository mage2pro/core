<?php
use Magento\Cms\Model\Block as B;
use Magento\Cms\Model\BlockRepository as BR;
use Magento\Cms\Model\ResourceModel\Block\Collection as C;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2018-05-21
 * 2024-03-13
 * 1) @see df_block()
 * 2) $id can be a numeric ID or a literal ID:
 * @see \Magento\Cms\Model\ResourceModel\Block::load()
 * @see \Magento\Cms\Model\ResourceModel\Block::getBlockId()
 * @used-by df_cms_block_content()
 * @param B|int|string $b
 * @throws NSE
 */
function df_cms_block($b):B {return $b instanceof B ? $b : df_cms_block_r()->getById($b);}

/**
 * 2018-05-21
 * 2024-03-13
 * 1) @see df_block_output()
 * 2) $id can be a numeric ID or a literal ID:
 * @see \Magento\Cms\Model\ResourceModel\Block::load()
 * @see \Magento\Cms\Model\ResourceModel\Block::getBlockId()
 * @used-by \AlbumEnvy\Popup\Settings::content()
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/catalog/category/l2/faq.phtml (https://github.com/cabinetsbay/site/issues/112)
 * @param int|string $id
 * @param Closure|bool|string $onE [optional]
 * @throws NSE
 */
function df_cms_block_content($id, $onE = ''):string {return df_try(function() use($id):string {return
	df_cms_block($id)->getContent()
;}, $onE);}

/**
 * 2018-05-21
 * @used-by df_cms_block()
 */
function df_cms_block_r():BR {return df_o(BR::class);}

/**
 * 2018-05-21
 * @used-by \Df\Config\Source\Block::map()
 */
function df_cms_blocks():C {return df_new_om(C::class);}
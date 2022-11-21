<?php
use Magento\Cms\Model\Block as B;
use Magento\Cms\Model\BlockRepository as BR;
use Magento\Cms\Model\ResourceModel\Block\Collection as C;
use Magento\Framework\Exception\NoSuchEntityException as NSE;

/**
 * 2018-05-21
 * @used-by df_cms_block_content()
 * @param B|int $b
 * @throws NSE
 */
function df_cms_block($b):B {return $b instanceof B ? $b : df_cms_block_r()->getById($b);}

/**
 * 2018-05-21
 * @used-by \AlbumEnvy\Popup\Settings::content()
 * @param Closure|bool|mixed $onError [optional]
 * @return string|null
 * @throws NSE
 */
function df_cms_block_content(int $id, $onError = null) {return df_try(function() use($id) {return
	df_cms_block($id)->getContent()
;}, $onError);}

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
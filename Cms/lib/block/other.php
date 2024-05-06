<?php
use Magento\Cms\Model\Block as B;
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
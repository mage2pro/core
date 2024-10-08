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
 * 2024-05-06
 * 1) "Improve `df_cms_block*` functions": https://github.com/mage2pro/core/issues/365
 * 2.1) @uses \Magento\Cms\Model\BlockRepository::getById() works identically to
 * @see \Magento\Cms\Model\GetBlockByIdentifier::execute()
 * because the `$storeId` parameter of @see \Magento\Cms\Model\GetBlockByIdentifier::execute() is ignored:
 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Cms/Model/BlockRepository.php#L142-L157
 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Cms/Model/GetBlockByIdentifier.php#L40-L54
 * 2.2) @see \Magento\Cms\Model\GetBlockByIdentifier is available in Magento ≥ 2.3.
 * @used-by df_cms_block()
 * @param B|int|string $b
 * @param Closure|bool|string $onE [optional]
 * @return B|null
 * @throws NSE
 */
function df_cms_block_get($b, $onE = null) {return $b instanceof B ? $b : df_try(function() use($b):B {return
	df_cms_block_r()->getById($b)
;}, $onE);}
<?php
use Magento\Framework\App\Cache\Type\Block as TBlock;
use Magento\PageCache\Model\Cache\Type as TPage;
use \Magento\Framework\App\Cache\Type\Config as TConfig;

/**
 * 2021-09-22
 * @used-by \Df\Directory\Plugin\Model\Currency::afterSaveRates()
 */
function df_cache_clean_blocks():void {df_cache_clean_type(TBlock::TYPE_IDENTIFIER);}

/**
 * 2021-09-22
 * @used-by df_cfg_save_cc()
 */
function df_cache_clean_cfg():void {df_cache_clean_type(TConfig::TYPE_IDENTIFIER);}

/**
 * 2021-09-22
 * @used-by \Df\Directory\Plugin\Model\Currency::afterSaveRates()
 */
function df_cache_clean_pages():void {df_cache_clean_type(TPage::TYPE_IDENTIFIER);}

/**
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @uses \Magento\Framework\App\Cache\TypeList::cleanType()
 * @used-by df_cache_clean_blocks()
 * @used-by df_cache_clean_cfg()
 * @used-by df_cache_clean_pages()
 * @param string $t
 */
function df_cache_clean_type($t):void {df_cache_type_list()->cleanType($t);}
<?php
use Magento\Framework\Cache\FrontendInterface as IFrontend;

/**
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @used-by \Df\OAuth\App::getAndSaveTheRefreshToken()
 * @used-by \Dfe\Moip\Backend\Enable::dfSaveAfter()
 */
function df_cache_clean() {
	df_map(function(IFrontend $f) {$f->getBackend()->clean();}, df_cache_pool());
	df_ram()->reset();
	/**
	 * 2017-10-19
	 * It is important, because M2 caches the configuration values in RAM:
	 * @see \Magento\Config\App\Config\Type\System::get()
	 */
	df_cfg_m()->clean();
}

/**
 * 2017-08-11
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @uses \Magento\Framework\App\Cache\TypeList::cleanType()
 * @used-by \Df\API\Client::p()
 * @param string $tag
 */
function df_cache_clean_tag($tag) {
	df_cache()->clean([$tag]);
	df_ram()->clean($tag);
}

/**
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @uses \Magento\Framework\App\Cache\TypeList::cleanType()
 * @used-by \Df\Directory\Plugin\Model\Currency::afterSaveRates()
 * @param string ...$types
 */
function df_cache_clean_types(...$types) {array_map([df_cache_type_list(), 'cleanType'], dfa_flatten(func_get_args()));;}

<?php
/**
 * 2015-10-05
 * @used-by \Dfe\GoogleBackendLogin\Block\Metadata::_construct()
 */
function df_metadata(string $k, string $v):void {
	if (!df_nes($v)) {
		df_page_config()->setMetadata($k, $v);
	}
}

/**
 * 2017-05-07
 * «How to set the title for the current page programmatically?» https://mage2.pro/t/3908
 * «How is @uses \Magento\Framework\View\Page\Title::set() implemented and used?» https://mage2.pro/t/3909
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 */
function df_page_title(string $s):void {df_page_config()->getTitle()->set($s);}
<?php
/**
 * 2024-06-11 "Implement `df_body_class()`": https://github.com/mage2pro/core/issues/420
 * @used-by CabinetsBay\Catalog\Observer\LayoutLoadBefore::execute() (https://github.com/cabinetsbay/catalog/issues/3)
 * @param string|string[] $c
 */
function df_body_class(...$c):void {df_call_a($c, function(string $c):void {
	/**
	 * 2024-06-10
	 * @uses \Magento\Framework\View\Page\Config::addBodyClass() does not allow spaces:
	 * 		$className = preg_replace('#[^a-z0-9-_]+#', '-', strtolower($className));
	 * https://github.com/magento/magento2/blob/2.4.7/lib/internal/Magento/Framework/View/Page/Config.php#L548
	 */
	foreach (df_explode_space($c) as $i) {/** @var string $i */
		df_page_config()->addBodyClass($i);
	}
});}

/**
 * 2015-10-05
 * @used-by Dfe\GoogleBackendLogin\Block\Metadata::_construct()
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
 * @used-by Dfe\Portal\Controller\Index\Index::execute()
 */
function df_page_title(string $s):void {df_page_config()->getTitle()->set($s);}
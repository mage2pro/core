<?php
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;
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
 * 2015-10-05
 * @used-by df_metadata()
 * @used-by df_page_title()
 * @used-by \Df\Sso\Button::_prepareLayout()
 * @used-by \Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 */
function df_page_config():Config {return df_o(Config::class);}

/**
 * 2017-05-07
 * «How to set the title for the current page programmatically?» https://mage2.pro/t/3908
 * «How is @uses \Magento\Framework\View\Page\Title::set() implemented and used?» https://mage2.pro/t/3909
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @param string $s
 */
function df_page_title($s):void {df_page_config()->getTitle()->set($s);}

/**
 * 2017-05-05
 * 2017-05-07
 * $template is a custom root template instead of «Magento_Theme::root.phtml».
 * https://github.com/magento/magento2/blob/2.1.6/app/etc/di.xml#L559-L565
 * «How is the root HTML template (Magento_Theme::root.phtml) declared and implemented?»
 * https://mage2.pro/t/3900
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @param string|null $template [optional]
 * @param string ...$handles [optional]
 */
function df_page_result($template = null, ...$handles):ResultPage {
	$f = df_o(PageFactory::class);/** @var PageFactory $f */
	$r = $f->create(false, df_clean(['template' => $template])); /** @var ResultPage $r */
	foreach ($handles as $h) {
		$r->addHandle($h);
	}
	return $r;
}
<?php
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;
/**
 * 2015-10-05
 * @param string $name
 * @param string|null $value
 */
function df_metadata($name, $value) {
	if (!is_null($value) && '' !== $value) {
		df_page_config()->setMetadata($name, $value);
	}
}

/**
 * 2015-10-05
 * @used-by df_metadata()
 * @used-by df_page_title()
 * @used-by \Df\Sso\Button::_prepareLayout()
 * @return Config
 */
function df_page_config() {return df_o(Config::class);}

/**
 * 2017-05-07
 * «How to set the title for the current page programmatically?» https://mage2.pro/t/3908
 * «How is @uses \Magento\Framework\View\Page\Title::set() implemented and used?» https://mage2.pro/t/3909
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @param string $s
 */
function df_page_title($s) {df_page_config()->getTitle()->set($s);}

/**
 * 2017-05-05
 * 2017-05-07
 * $template is a custom root template instead of «Magento_Theme::root.phtml».
 * https://github.com/magento/magento2/blob/2.1.6/app/etc/di.xml#L559-L565
 * «How is the root HTML template (Magento_Theme::root.phtml) declared and implemented?»
 * https://mage2.pro/t/3900
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 * @param string|null $template [optional]
 * @param ...string[] $handles [optional]
 * @return ResultPage
 */
function df_page_result($template = null, ...$handles) {
	$f = df_o(PageFactory::class);/** @var PageFactory $f */
	$r = $f->create(false, df_clean(['template' => $template])); /** @var ResultPage $r */
	foreach ($handles as $h) {
		$r->addHandle($h);
	}
	return $r;
}
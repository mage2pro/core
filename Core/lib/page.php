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
 * @used-by \Df\Sso\Button::_prepareLayout()
 * @return Config
 */
function df_page_config() {return df_o(Config::class);}

/**
 * 2017-05-05
 * @used-by \Dfe\Cms\Controller\Index\Index::execute()
 * @param string|null $handle [optional]
 * @return ResultPage
 */
function df_page_result($handle = null) {
	/** @var PageFactory $f */
	$f = df_o(PageFactory::class);
	/** @var ResultPage $result */
	$result = $f->create();
	if ($handle) {
		$result->addHandle($handle);
	}
	return $result;
}
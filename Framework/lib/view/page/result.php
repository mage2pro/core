<?php
use Magento\Framework\View\Result\Page as P;
use Magento\Framework\View\Result\PageFactory as F;

/**
 * 2017-05-05
 * 2017-05-07
 * $template is a custom root template instead of «Magento_Theme::root.phtml».
 * https://github.com/magento/magento2/blob/2.1.6/app/etc/di.xml#L559-L565
 * «How is the root HTML template (Magento_Theme::root.phtml) declared and implemented?»
 * https://mage2.pro/t/3900
 * @used-by \Dfe\Portal\Controller\Index\Index::execute()
 */
function df_page_result(string $template = '', string ...$handles):P {
	$f = df_o(F::class);/** @var F $f */
	$r = $f->create(false, df_clean(['template' => $template])); /** @var P $r */
	foreach ($handles as $h) {
		$r->addHandle($h);
	}
	return $r;
}
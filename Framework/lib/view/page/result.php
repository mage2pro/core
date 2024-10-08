<?php
use Magento\Framework\View\Result\Page as R;

/**
 * 2017-05-05
 * 2017-05-07
 * 1) $template is a custom root template instead of «Magento_Theme::root.phtml».
 * https://github.com/magento/magento2/blob/2.1.6/app/etc/di.xml#L559-L565
 * 2) «How is the root HTML template (Magento_Theme::root.phtml) declared and implemented?»: https://mage2.pro/t/3900
 * @used-by Dfe\Portal\Controller\Index\Index::execute()
 */
function df_page_result(string $t = '', string ...$handles):R {
	$r = df_page_factory()->create(false, df_clean(['template' => $t])); /** @var R $r */
	foreach ($handles as $h) {/** @var string $h */
		$r->addHandle($h);
	}
	return $r;
}
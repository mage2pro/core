<?php
/**
 * 2016-11-22
 * $m could be:
 * 		1) A module name: «A_B»
 * 		2) A class name: «A\B\C».
 * 		3) An object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 * 2024-05-23 "Improve `df_block_output()`": https://github.com/mage2pro/core/issues/387
 * @see df_cms_block()
 * @used-by df_block_echo()
 * @used-by Dfe\Facebook\I::init()
 * @used-by Dfe\Moip\Block\Info\Boleto::rCustomerAccount()
 * @used-by Dfe\Stripe\Block\Multishipping::_toHtml()
 * @used-by Inkifi\Map\HTML::tiles()
 * @used-by KingPalm\B2B\Block\Registration::_toHtml()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/header.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l2/l3/items.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs/matching-styles.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products/not-empty.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/products/not-empty.phtml (https://github.com/cabinetsbay/catalog/issues/38)
 * @param string|object|null $c
 * @param array $vars [optional]
 * @param array(string => mixed) $data [optional]
 */
function df_block_output($c, string $t = '', array $vars = [], array $d = []):string {return df_block(...(
	df_es($t)
		? [$c, $d, '', $vars]
		: [null, $d, df_asset_name($t, df_contains($t, '::') ? null : df_module_name($c)), $vars]
/**
 * @uses \Magento\Framework\View\Element\AbstractBlock::toHtml()
 * @uses \Magento\Framework\View\Element\BlockInterface::toHtml()
 */
))->toHtml();}
<?php
use Df\Core\O;
use Magento\Backend\Block\Template as BackendT;
use Magento\Framework\View\Element\AbstractBlock as AB;
use Magento\Framework\View\Element\BlockInterface as IB;
use Magento\Framework\View\Element\Template as T;
/**
 * @see df_cms_block_get()
 * @used-by df_block_output()
 * @used-by BlushMe\Checkout\Block\Extra\Item::part()
 * @used-by Dfe\Dynamics365\Button::getElementHtml()
 * @used-by Dfe\Klarna\Observer\ShortcutButtonsContainer::execute()
 * @param string|O|null $c
 * 2015-12-14
 * $c может быть как объектом, так и строкой: https://3v4l.org/udMMH
 * @param string|array(string => mixed) $data [optional]
 * 2016-11-22
 * @param array(string => mixed) $vars [optional]
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 * @return AB|IB|T|BackendT
 */
function df_block($c, $data = [], string $t = '', array $vars = []):IB {
	if (is_string($data)) {
		$t = $data;
		$data = [];
	}
	/**
	 * 2016-11-22
	 * В отличие от Magento 1.x, в Magento 2 нам нужен синтаксис ['data' => $data]:
	 * @see \Magento\Framework\View\Layout\Generator\Block::createBlock():
	 * $block->addData(isset($arguments['data']) ? $arguments['data'] : []);
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/Layout/Generator/Block.php#L240
	 * В Magento 1.x было не так:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.1/app/code/core/Mage/Core/Model/Layout.php#L482-L491
	 * @uses \Magento\Framework\View\Layout::createBlock()
	 * @uses \Magento\Framework\View\LayoutInterface::createBlock()
	 * @var AB|IB|T|BackendT $r
	 */
	$r = df_layout()->createBlock($c ?: (df_is_backend() ? BackendT::class : T::class), dfa($data, 'name'), ['data' => $data]);
	if ($r instanceof T) { # 2019-06-11
		$r->assign($vars); # 2016-11-22
	}
	if ($t && $r instanceof T) {
		$r->setTemplate(df_phtml_add_ext($t));
	}
	return $r;
}
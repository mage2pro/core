<?php
use Df\Core\O;
use Df\Theme\Model\View\Design as DfDesign;
use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Layout\ProcessorInterface as IProcessor;
use Magento\Framework\View\LayoutInterface as ILayout;
use Magento\Framework\View\Model\Layout\Merge;
/**
 * @used-by df_block_output()
 * @used-by \BlushMe\Checkout\Block\Extra\Item::part()
 * @used-by \Dfe\Dynamics365\Button::getElementHtml()
 * @used-by \Dfe\Klarna\Observer\ShortcutButtonsContainer::execute()
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
 * @return AbstractBlock|BlockInterface|Template
 */
function df_block($c, $data = [], string $template = '', array $vars = []) {
	if (is_string($data)) {
		$template = $data;
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
	 */
	/** @var AbstractBlock|BlockInterface|Template $r */
	$r = df_layout()->createBlock(
		$c ?: (df_is_backend() ? BackendTemplate::class : Template::class), dfa($data, 'name'), ['data' => $data]
	);
	# 2019-06-11
	if ($r instanceof Template) {
		# 2016-11-22
		$r->assign($vars);
	}
	if ($template && $r instanceof Template) {
		$r->setTemplate(df_file_ext_add($template, 'phtml'));
	}
	return $r;
}

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
 * @used-by \Df\Facebook\I::init()
 * @used-by \Dfe\Moip\Block\Info\Boleto::rCustomerAccount()
 * @used-by \Dfe\Stripe\Block\Multishipping::_toHtml()   
 * @used-by \Inkifi\Map\HTML::tiles()
 * @used-by \KingPalm\B2B\Block\Registration::_toHtml()
 * @param string|object|null $m
 * @param array $vars [optional]
 * @param array(string => mixed) $data [optional]
 */
function df_block_output($m, string $t = '', array $vars = [], array $data = []):string {return !$t
	? df_block($m, $data, null, $vars)->toHtml()
	: df_block(
		null
		,$data
		,df_asset_name($t, df_contains($t, '::') ? null : df_module_name($m))
		,$vars
	)->toHtml()
;}

/**
 * @used-by df_block()
 * @used-by df_layout_update()
 * @used-by df_parent_name()
 * @used-by \KingPalm\B2B\Block\Registration::v()
 * @return Layout|ILayout
 */
function df_layout() {return df_o(ILayout::class);}

/**
 * 2017-10-16
 * @used-by df_handles()
 * @used-by \Df\Framework\Plugin\App\Action\AbstractAction::beforeExecute() 
 * @param Closure|bool|mixed $onError [optional]
 * @return IProcessor|Merge
 */
function df_layout_update($onError = true) {return df_try(function() {
	df_assert(DfDesign::isThemeInitialized(),
		'This attempt to call Magento\Framework\View\Layout::getUpdate() can break the Magento frontend.'
	);
	return df_layout()->getUpdate();
}, $onError);}

/**
 * 2016-11-30
 * Наивное $e->getParentBlock()->getNameInLayout() некорректно,
 * потому что родительским элементом для $e может быть не только блок,
 * но и контейнер, и тогда $e->getParentBlock() вернёт false.
 * @param AbstractBlock|string $e
 * @return string|null
 */
function df_parent_name($e) {return df_ftn(df_layout()->getParentName(
	$e instanceof AbstractBlock ? $e->getNameInLayout() : $e
));}
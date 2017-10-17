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
 * 2015-12-14
 * Добавил возможность передачи в качестве первого параметра @see O
 * причём как в виде объекта, так и строки-класса.
 *
 * Такая возможность позволяет нам эффективно рендерить шаблоны без иерархии своих классов-блоков.
 * В Российской сборке для Magento 1.x
 * нам приходилось дублировать один и тот же код в классе базовой модели (аналоге класса O),
 * и в 2-х базовых классах блоков (абстрактном и блоке с шаблоном), т.е. в 3 местах.
 * Теперь же нам этого делать не нужно.
 *
 * @used-by df_block_output()
 * @used-by \Dfe\Dynamics365\Button::getElementHtml()
 * @used-by \Dfe\Klarna\Observer\ShortcutButtonsContainer::execute()
 *
 * @param string|O|null $type
 * 2015-12-14
 * $type может быть как объектом, так и строкой: https://3v4l.org/udMMH
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 *
 * 2016-11-22
 * @param array $vars [optional]
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 *
 * @return AbstractBlock|BlockInterface|Template
 */
function df_block($type, $data = [], $template = null, array $vars = []) {
	/** @var O $context */
	if (!is_a($type, O::class, true)) {
		$context = null;
	}
	else {
		$context = is_object($type) ? $type : new $type;
		$type = null;
	}
	if (is_null($type)) {
		$type = df_is_backend() ? BackendTemplate::class : Template::class;
	}
	/** @var string|null $template */
	if (is_string($data)) {
		$template = $data;
		$data = [];
	}
	/** @var AbstractBlock|BlockInterface|Template $result */
	/**
	 * 2016-11-22
	 * В отличие от Magento 1.x, в Magento 2 нам нужен синтаксис ['data' => $data]:
	 * @see \Magento\Framework\View\Layout\Generator\Block::createBlock():
	 * $block->addData(isset($arguments['data']) ? $arguments['data'] : []);
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/Layout/Generator/Block.php#L240
	 * В Magento 1.x было не так:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.1/app/code/core/Mage/Core/Model/Layout.php#L482-L491
	 */
	$result = df_layout()->createBlock($type, dfa($data, 'name'), ['data' => $data]);
	// 2016-11-22
	$result->assign($vars);
	if ($template && $result instanceof Template) {
		$result->setTemplate(df_append($template, '.phtml'));
	}
	if ($context) {
		// 2016-11-22
		// «Sets the object that should represent $block in template.»
		$result->setTemplateContext($context);
	}
	return $result;
}

/**
 * 2016-11-22
 * @param string|object $m
 * $m could be:
 * 1) A module name: «A_B».
 * 2) A class name: «A\B\C».
 * 3) An object. It is reduced to case 2 via @see get_class()
 * @param string $template [optional]
 * @param array $vars [optional]
 * Параметры $vars будут доступны в шаблоне в качестве переменных:
 * @see \Magento\Framework\View\TemplateEngine\Php::render()
 *		extract($dictionary, EXTR_SKIP);
 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/TemplateEngine/Php.php#L58
 * @used-by \Df\Facebook\I::init()
 * @used-by \Dfe\Moip\Block\Info\Boleto::rCustomerAccount()
 * @return string
 */
function df_block_output($m, $template, array $vars = []) {return
	df_block(null, [], df_module_name($m) . "::$template", $vars)->toHtml()
;}

/**
 * @used-by df_block()
 * @used-by df_layout_update()
 * @used-by df_parent_name()
 * @return Layout|ILayout
 */
function df_layout() {return df_o(ILayout::class);}

/**
 * 2017-10-16
 * @used-by df_handles()
 * @used-by \Df\Framework\Plugin\App\Action\AbstractAction::beforeExecute() 
 * @param \Closure|bool|mixed $onError [optional]
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
function df_parent_name($e) {return df_ftn(
	df_layout()->getParentName($e instanceof AbstractBlock ? $e->getNameInLayout() : $e)
);}
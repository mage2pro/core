<?php
use Df\Core\O;
use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;
/**
 * 2015-12-14
 * Добавил возможность передачи в качестве первого параметра @see O
 * причём как в виде объекта, так и строки-класса.
 *
 * Такая возможность позволяет нам эффективно рендерить шаблоны
 * без иерархии своих классов-блоков.
 * В Российской сборке для Magento 1.x
 * нам приходилось дублировать один и тот же код в классе базовой модели (аналоге класса O),
 * и в 2-х базовых классах блоков (абстрактном и блоке с шаблоном), т.е. в 3 местах.
 * Теперь же нам этого делать не нужно.
 *
 * @param string|O $type
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 * @return AbstractBlock|BlockInterface|Template
 */
function df_block($type, $data = [], $template = null) {
	/**
	 * 2015-12-14
	 * $type может быть как объектом, так и строкой:
	 * https://3v4l.org/udMMH
	 */
	/** @var O $context */
	if (!is_a($type, O::class, true)) {
		$context = null;
	}
	else {
		$context = is_object($type) ? $type : new $type;
		$type = df_is_admin() ? BackendTemplate::class : Template::class;
	}
	/** @var string|null $template */
	if (is_string($data)) {
		$template = $data;
		$data = [];
	}
	/** @var AbstractBlock|BlockInterface|Template $result */
	$result = df_layout()->createBlock($type, df_a($data, 'name'), $data);
	if ($template && $result instanceof Template) {
		$result->setTemplate($template);
	}
	if ($context) {
		$result->setTemplateContext($context);
	}
	return $result;
}

/**
 * 2015-12-14
 * Добавил возможность передачи в качестве первого параметра @see O
 * @param string|O $type
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 * @return string
 */
function df_block_r($type, $data = [], $template = null) {
	return df_block($type, $data, $template)->toHtml();
}

/**
 * 2015-12-21
 * @return string[]
 */
function df_handles() {return df_layout()->getUpdate()->getHandles();}

/** @return \Magento\Framework\View\Layout|\Magento\Framework\View\LayoutInterface */
function df_layout() {return df_o(\Magento\Framework\View\LayoutInterface::class);}




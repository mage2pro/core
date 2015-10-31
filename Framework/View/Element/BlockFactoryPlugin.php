<?php
namespace Df\Framework\View\Element;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\Element\BlockInterface;
class BlockFactoryPlugin {
	/**
	 * 2015-09-19
	 * Цель метода — сделать компонент доступным внешним классам.
	 * @see \Magento\Ui\Component\Wrapper\UiComponent::$component
	 * @see BlockFactory::createBlock()
	 * @param BlockFactory $subject
	 * @param \Closure $proceed
	 * @param string $blockName
	 * @param mixed[] $arguments [optional]
	 * @return BlockInterface
	 */
	public function aroundCreateBlock(
		BlockFactory $subject, \Closure $proceed, $blockName, array $arguments = []
	) {
		/** @var BlockInterface|AbstractBlock $result */
		$result = $proceed($blockName, $arguments);
		$result[self::COMPONENT] = df_a($arguments, 'component');
		return $result;
	}

	/**
	 * @used-by aroundCreateBlock()
	 * @used-by \Df\Framework\View\LayoutPlugin::aroundRenderNonCachedElement()
	 */
	const COMPONENT = 'df_component';
}
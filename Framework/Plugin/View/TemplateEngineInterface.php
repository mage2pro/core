<?php
namespace Df\Framework\Plugin\View;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\TemplateEngineInterface as Sb;
class TemplateEngineInterface {
	/**
	 * 2015-09-02
	 * А вот попадаем ли мы сюда для блоков без шаблонов
	 * (и надо ли нам тогда сюда попадать)?
	 * @see \Magento\Framework\View\TemplateEngineInterface::render()
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param BlockInterface $block
	 * @param string $templateFile
	 * @param mixed[] $dictionary
	 * @return string
	 */
	public function aroundRender(
		Sb $sb, \Closure $proceed, BlockInterface $block, $templateFile, array $dictionary = []
	) {
		/** @var string $result */
		df_state()->blockSet($block, $templateFile);
		try {$result = $proceed($block, $templateFile, $dictionary);}
		finally {df_state()->blockSetPrev();}
		return $result;
	}
}
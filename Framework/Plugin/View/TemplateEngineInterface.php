<?php
namespace Df\Framework\Plugin\View;
use Magento\Framework\View\Element\BlockInterface as B;
use Magento\Framework\View\TemplateEngineInterface as Sb;
final class TemplateEngineInterface {
	/**
	 * 2015-09-02
	 * А вот попадаем ли мы сюда для блоков без шаблонов
	 * (и надо ли нам тогда сюда попадать)?
	 * @see \Magento\Framework\View\TemplateEngineInterface::render()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param B $b
	 * @param string $templateFile
	 * @param mixed[] $dictionary
	 * @return string
	 */
	function aroundRender(Sb $sb, \Closure $f, B $b, $templateFile, array $dictionary = []) {
		/** @var string $result */
		df_state()->blockSet($b, $templateFile);
		try {$result = $f($b, $templateFile, $dictionary);}
		finally {df_state()->blockSetPrev();}
		return $result;
	}
}
<?php
// 2016-11-21
namespace Df\Framework\Plugin\View\Element;
use Magento\Framework\View\Element\AbstractBlock as Sb;
class AbstractBlock {
	/**
	 * 2016-11-21
	 * Цель плагина — устранение дефекта метода
	 * @see \Magento\Framework\View\Element\AbstractBlock::extractModuleName(),
	 * который работает некорректно (возвращает пустую строку),
	 * если класс модуля не имеет префикса «Block»:
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L846-L860
	 * @see \Magento\Framework\View\Element\AbstractBlock::getModuleName()
	 *
	 * @param Sb $sb
	 * @param string $r
	 * @return string
	 */
	function afterGetModuleName(Sb $sb, $r) {return
		$r ?: $sb['module_name'] = df_module_name($sb)
	;}
}
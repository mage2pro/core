<?php
namespace Df\Config\Plugin\Model\Config\Structure\Element\Iterator;
use Magento\Config\Model\Config\Structure\Element\Iterator\Tab as Sb;
final class Tab {
	/**
	 * 2015-11-14
	 * Цель плагина — алфавитное упорядочивание моих модулей
	 * в разделе административных настроек модулей.
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator\Tab::setElements()
	 * @param Sb $sb
	 * @param array(string => array(string => string)) $elements
	 * @param string $scope
	 * @return array
	 */
	function beforeSetElements(Sb $sb, array $elements, $scope) {
		/** @var array(string => string)|null $sections */
		if ($sections = dfa_deep($elements, '_df/children')) {
			uasort($sections,
				/**
				 * @param array(string => string) $a
				 * @param array(string => string) $b
				 * @return int
				 */
				function($a, $b) {return strcasecmp(dfa($a, 'label'), dfa($b, 'label'));}
			);
			$elements['_df']['children'] = $sections;
		}
		return [$elements, $scope];
	}
}
<?php
namespace Df\Config\Plugin\Model\Config\Structure\Element\Iterator;
use Magento\Config\Model\Config\Structure\Element\Iterator\Tab as Sb;
# 2015-11-14
final class Tab {
	/**
	 * 2015-11-14 Цель плагина — алфавитное упорядочивание моих модулей в разделе административных настроек модулей.
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator\Tab::setElements()
	 * @param array(string => array(string => string)) $e
	 */
	function beforeSetElements(Sb $sb, array $e, string $scope):array {
		if ($sections = dfa_deep($e, '_df/children')) {/** @var array(string => string)|null $sections */
			uasort($sections,
				/**
				 * @param array(string => string) $a
				 * @param array(string => string) $b
				 */
				function(array $a, array $b):int {return strcasecmp(dfa($a, 'label'), dfa($b, 'label'));}
			);
			$e['_df']['children'] = $sections;
		}
		return [$e, $scope];
	}
}
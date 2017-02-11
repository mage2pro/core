<?php
namespace Df\Config\Model\Config;
class Structure extends \Magento\Config\Model\Config\Structure {
	/**
	 * 2016-08-02
	 * @param string $tabName
	 * @param string $tabProperty
	 * @return string|null
	 */
	static function tab($tabName, $tabProperty) {
		/** @var Structure $s */
		$s = df_config_structure();
		if (!isset($s->_data['tabs'])) {
			$s->getTabs();
		}
		return dfa(dfa($s->_data['tabs'], $tabName, []), $tabProperty);
	}
}
<?php
namespace Df\Config\Model\Config;
class Structure extends \Magento\Config\Model\Config\Structure {
	/**
	 * 2016-08-02
	 * @used-by df_config_tab_label()
	 * @param string $tabName
	 * @param string $tabProperty
	 * @return string|null
	 */
	static function tab($tabName, $tabProperty) {
		$s = df_config_structure(); /** @var Structure $s */
		if (!isset($s->_data['tabs'])) {
			$s->getTabs();
		}
		return dfa(dfa($s->_data['tabs'], $tabName, []), $tabProperty);
	}
}
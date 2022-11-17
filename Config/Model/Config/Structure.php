<?php
namespace Df\Config\Model\Config;
class Structure extends \Magento\Config\Model\Config\Structure {
	/**
	 * 2016-08-02
	 * @used-by df_config_tab_label()
	 */
	static function tab(string $tabName, string $tabProperty):string {
		$s = df_config_structure(); /** @var Structure $s */
		if (!isset($s->_data['tabs'])) {
			$s->getTabs();
		}
		return df_nts(dfa(dfa($s->_data['tabs'], $tabName, []), $tabProperty));
	}
}
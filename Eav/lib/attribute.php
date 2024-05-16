<?php
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Framework\Model\AbstractModel as M;
/**
 * 2019-06-15
 * @used-by df_customer_att_pos_set()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @see df_product_attrs_r()
 */
function df_att_code2id(string $c):int {return df_first(df_fetch_col_int(
	'eav_attribute', 'attribute_id', 'attribute_code', $c
));}

/**
 * 2024-05-16 "Implement `df_att_val_s()`": https://github.com/mage2pro/core/issues/373
 * @uses \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::getOptionText()
 */
function df_att_val(M $m, A $a, string $k, string $d = ''):string {return df_fnes($r = $m[$a->getAttributeCode()]) ? $d : (
	!$a->usesSource() ? $r : (
		/**
		 * 2020-01-31
		 * @see \Magento\Eav\Model\Entity\Attribute\Source\Table::getOptionText() can return an empty array
		 * for an attribute's value (e.g., for the `description` attribute), if the value contains a comma.
		 */
		is_array($r = $a->getSource()->getOptionText($prev = $r)) ? $prev : (
			df_fnes($r) ? $d : $r
		)
	)
);}
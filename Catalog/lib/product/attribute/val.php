<?php
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute as A;
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2024-05-16 "Implement `df_att_val_s()`": https://github.com/mage2pro/core/issues/373
 * @uses \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource::getOptionText()
 * @used-by df_product_att_val()
 */
function df_att_val(M $m, A $a, string $d = ''):string {return df_fnes($r = $m[$a->getAttributeCode()]) ? $d : (
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
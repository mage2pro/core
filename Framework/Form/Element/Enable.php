<?php
namespace Df\Framework\Form\Element;
class Enable extends Checkbox {
	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\Form\Element\Checkbox::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
	 * @return string|null
	 */
	public function getComment() {
		/** @var string|null $result */
		$result = parent::getComment();
		/** @var string|null $vc */
		$vc = df_fe_fc($this, 'dfValidator');
		if ($vc) {
			/** @var Enabler $v */
			$v = df_o($vc);
			/** @var string[]|true $messages */
			$messages = $v->check();
			if (is_array($messages)) {
				$result .= df_tag_list($messages, false, 'df-enabler-warnings');
			}	
		}
		return $result;
	}
}
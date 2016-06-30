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
		if ($this->enabler()) {
			/** @var string[]|true $messages */
			$messages = $this->enabler()->check();
			if (is_array($messages)) {
				$result .= df_tag('ul', 'df-enabler-warnings', df_cc_n(
					array_map(function($message) {return df_tag('li', null, $message);}, $messages)
				));
			}
		}
		return $result;
	}

	/**
	 * 2016-06-30
	 * @return Enabler|null
	 */
	private function enabler() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $class */
			$class = df_fe_fc($this, 'dfEnabler');
			$this->{__METHOD__} = df_n_set(!$class ? null : df_o($class));
		}
		return df_n_get($this->{__METHOD__});
	}
}
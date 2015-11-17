<?php
namespace Df\Framework\Data\Form\Element;
class Fieldset extends \Magento\Framework\Data\Form\Element\Fieldset {
	/**
	 * 2015-11-17
	 * @override
	 * @see \Magento\Framework\Data\Form\AbstractForm::_construct()
	 * @used-by \Magento\Framework\Data\Form\AbstractForm::__construct()
	 * @return void
	 */
	protected function _construct() {
		$this->addClass('df-fieldset');
		parent::_construct();
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $label
	 * @param mixed $value [optional]
	 * @return \Magento\Framework\Data\Form\Element\Checkbox
	 */
	protected function checkbox($name, $label, $value = null) {
		return $this->field($name, 'checkbox', $label, $value, ['checked' => $value])
			// Ядро никакого специфического для checkbox класса не добавляет
			->addClass('df-checkbox')
		;
	}

	/**
	 * 2015-11-17
	 * Независимые поля имеют имена: groups[frontend][fields][value__font__emphase__bold][value]
	 * У нас же имя будет: groups[frontend][fields][value__font][df_children][emphase__bold]
	 * @param string $name
	 * @return string
	 */
	protected function cn($name) {
		return "groups[{$this->group()}][fields][{$this->nameShort()}][df_children][{$name}]";
	}

	/**
	 * 2015-11-17
	 * @param string|null $key [optional]
	 * @return array(string => mixed)
	 */
	protected function fc($key = null) {
		/** @var array(string => mixed) $result */
		$result = $this['field_config'];
		return $key ? df_a($result, $key) : $result;
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $type
	 * @param string $label
	 * @param mixed $value [optional]
	 * @param array(string => mixed) $data [optional]
	 * @return \Magento\Framework\Data\Form\Element\AbstractElement
	 */
	protected function field($name, $type, $label, $value = null, $data = []) {
		return $this->addField($this->cn($name), $type, [
			'name' => $this->cn($name)
			,'label' => __($label)
			,'title' => __($label)
			,'value' => $value
		] + $data);
	}

	/**
	 * 2015-11-17
	 * @param string $name
	 * @param string $label
	 * @param mixed $value [optional]
	 * @return \Magento\Framework\Data\Form\Element\Select
	 */
	protected function yesNo($name, $label, $value = null) {
		return $this->field($name, 'select', $label, $value, ['values' => df_yes_no()]);
	}

	/** @return string */
	private function group() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_last(explode('/', $this->fc('path')));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function nameShort() {return $this->fc('id');}
}



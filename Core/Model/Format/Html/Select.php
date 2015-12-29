<?php
namespace Df\Core\Model\Format\Html;
final class Select extends \Df\Core\O {
	/**
	 * @used-by render()
	 * @return string
	 */
	private function _render() {
		return df_tag('select', $this->getAttributes(), $this->getOptionsAsHtml());
	}

	/** @return array(string => string) */
	private function getAttributes() {return $this->cfg(self::$P__ATTRIBUTES, []);}

	/** @return array(int|string => string)|array(array(string => int|string|mixed[])) */
	private function getOptions() {return $this->cfg(self::$P__OPTIONS);}

	/** @return string */
	private function getOptionsAsHtml() {
		return $this->implodeTags($this->renderOptions($this->getOptions()));
	}

	/** @return string|null */
	private function getSelected() {return $this->cfg(self::$P__SELECTED);}

	/**
	 * @param string[] $tags
	 * @return string
	 */
	private function implodeTags(array $tags) {return df_tab_multiline(df_cc_n($tags));}

	/**
	 * @param int|string $index
	 * @param string|array(array(string => int|string|mixed[])) $option
	 * @return string
	 */
	private function renderOption($index, $option) {
		/** @var string $result */
		if (!is_array($option)) {
			// опция имеет формат array('RU' => 'Россия')
			$result = $this->renderOptionTag($index, $option);
		}
		else {
			/** @var int|string|array(string => string)|array(array(string => string|array(string => string))) $value */
			$value = df_a($option, 'value');
			/** @var string $label */
			$label = df_a($option, 'label');
			if (!is_array($value)) {
				// опция имеет формат array('label' => 'Россия', 'value' => 'RU')
				$result = $this->renderOptionTag($value, $label);
			}
			else {
				// опция имеет формат array('label' => 'группа опций', 'value' => array(...))
				$result = df_tag('optgroup', ['label' => $label], $this->implodeTags(
					$this->renderOptions($value)
				));
			}
		}
		return $result;
	}

	/**
	 * @param array(int|string => string)|array(array(string => int|string|mixed[])) $options
	 * @return string[]
	 */
	private function renderOptions(array $options) {
		/** @var string[] $result */
		$result = [];
		foreach ($options as $key => $option) {
			/** @var int|string $key */
			/** @var string|array(array(string => int|string|mixed[])) $option */
			$result[]= $this->renderOption($key, $option);
		}
		return $result;
	}

	/**
	 * @param int|string $value
	 * @param string $label
	 * @return string
	 */
	private function renderOptionTag($value, $label) {
		/** @var array(string => string) $attributes */
		$attributes = ['value' => $value, 'label' => $label];
		if ($value === $this->getSelected()) {
			$attributes['selected'] = 'selected';
		}
		return df_tag('option', $attributes, $label);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__ATTRIBUTES, RM_V_ARRAY, false)
			->_prop(self::$P__OPTIONS, RM_V_ARRAY)
			->_prop(self::$P__SELECTED, RM_V_STRING, false)
		;
	}
	/** @var string */
	private static $P__ATTRIBUTES = 'attributes';
	/** @var string */
	private static $P__OPTIONS = 'options';
	/** @var string */
	private static $P__SELECTED = 'selected';

	/**
	 * @used-by df_html_select()
	 * @param array(int|string => string)|array(array(string => int|string|mixed[])) $options
	 * @param string|null $selected [optional]
	 * @param array(string => string) $attributes [optional]
	 * @return string
	 */
	public static function render(array $options, $selected = null, array $attributes = []) {
		/** @var Select $i */
		$i = new self([
			self::$P__OPTIONS => $options
			, self::$P__SELECTED => $selected
			, self::$P__ATTRIBUTES => $attributes
		]);
		return $i->_render();
	}
}
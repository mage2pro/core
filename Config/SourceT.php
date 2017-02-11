<?php
namespace Df\Config;
abstract class SourceT extends Source {
	/**
	 * 2016-08-07
	 * @used-by \Dfe\AllPay\Settings::methodsLabels()
	 * @param string[]|null $keys [optional]
	 * @return array(string => string)
	 */
	function options($keys = null) {
		/** @var array(string => string) $options */
		$options = $this->map();
		return df_translate_a(is_null($keys) ? $options : dfa_select_ordered($options, $keys));
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Config\Source::toOptionArray()
	 * @return array(array(string => string))
	 */
	function toOptionArray() {return df_map_to_options_t($this->map());}
}

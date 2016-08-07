<?php
namespace Df\Config;
abstract class SourceT extends Source {
	/**
	 * 2016-08-07
	 * @used-by \Dfe\AllPay\Settings::methodsLabels()
	 * @param string[]|null $keys [optional]
	 * @return string[]
	 */
	public function labels($keys = null) {
		/** @var array(string => string) $labels */
		$labels = $this->map();
		return array_values(df_translate_a(
			is_null($keys) ? $labels : dfa_select_ordered($labels, $keys)
		));
	}

	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Config\Source::toOptionArray()
	 * @return array(array(string => string))
	 */
	public function toOptionArray() {return df_map_to_options_t($this->map());}
}

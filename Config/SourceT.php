<?php
namespace Df\Config;
abstract class SourceT extends Source {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Config\Source::toOptionArray()
	 * @return array(array(string => string))
	 */
	public function toOptionArray() {return df_map_to_options_t($this->map());}
}

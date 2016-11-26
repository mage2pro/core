<?php
// 2016-11-23
namespace Df\Sso\Source\Button\Type;
class UNL extends UL {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @see \Df\Sso\Settings\Button::type()
	 * @return array(string => string)
	 */
	protected function map() {return dfa_insert(parent::map(), 1, ['N' => 'native button']);}
}
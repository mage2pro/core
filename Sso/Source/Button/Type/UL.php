<?php
/**
 * 2016-11-23
 * Не предоставляющие собственный дизайн для кнопок сервисы (например, «Blackbaud NetCommunity»)
 * используют этот класс, а предоставляющие — класс @used-by UNL
 */
namespace Df\Sso\Source\Button\Type;
class UL extends \Df\Config\SourceT {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return ['button-unified' => 'unified button', 'link' => 'link'];}
}
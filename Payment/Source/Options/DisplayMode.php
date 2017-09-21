<?php
namespace Df\Payment\Source\Options;
/**       
 * 2017-09-21                                                                                        
 * *) iPay88: https://github.com/mage2pro/ipay88/blob/1.3.3/etc/adminhtml/system.xml#L151-L164  
 * *) Robokassa: https://github.com/mage2pro/robokassa/blob/1.2.4/etc/adminhtml/system.xml#L230-L243
 * *) Yandex.Kassa: https://github.com/mage2pro/yandex-kassa/blob/0.1.5/etc/adminhtml/system.xml#L178-L192 
 */
final class DisplayMode extends \Df\Config\Source {
	/**
	 * 2017-09-21
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return [self::IMAGES => 'images', 'text' => 'text'];}

	/**
	 * 2017-09-21
	 * @used-by \Df\Payment\ConfigProvider::configOptions()
	 * @used-by \Df\Payment\Source\Options\DisplayMode::map()
	 */
	const IMAGES = 'images';
}
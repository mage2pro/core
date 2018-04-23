<?php
namespace Df\Config;
/**
 * 2017-09-18  
 * @see \Df\Payment\ConfigProvider
 * @see \Df\Payment\ConfigProvider\IOptions
 * @see \Df\Shipping\ConfigProvider
 */
interface ISettings {
	/**
	 * 2017-09-18   
	 * @used-by \Df\Payment\ConfigProvider::configOptions()
	 * @return Settings
	 */
	function s();
}
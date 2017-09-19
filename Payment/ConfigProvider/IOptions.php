<?php
namespace Df\Payment\ConfigProvider;
/**
 * 2017-09-18
 * @see \Df\GingerPaymentsBase\ConfigProvider
 * @see \Dfe\IPay88\ConfigProvider
 * @see \Dfe\Robokassa\ConfigProvider
 * @see \Dfe\YandexKassa\ConfigProvider
 */
interface IOptions extends \Df\Config\ISettings{
	/**   
	 * 2017-09-18
	 * You can return the result in one the following 2 formats:
	 * 1) ['a label' => 'a value']
	 * 2) [['label' => 'a label', 'value' => 'a value', 'children' => <...>]]
	 * @see Df_Payment/withOptions::woOptions():
	 * 		return($.isArray(o) ? o : $.map(o, function(v, k) {return {label: v, value: k};}));
	 * https://github.com/mage2pro/core/blob/2.12.5/Payment/view/frontend/web/withOptions.js#L101-L107
	 * @used-by \Df\Payment\ConfigProvider::configOptions() 
	 * @see \Df\GingerPaymentsBase\ConfigProvider::options()
	 * @see \Dfe\IPay88\ConfigProvider::options()
	 * @see \Dfe\Robokassa\ConfigProvider::options() 
	 * @see \Dfe\YandexKassa\ConfigProvider::options()
	 * @return array(<value> => <label>)|array(array('label' => string, 'value' => int|string, 'children' => <...>))
	 */
	function options();
}
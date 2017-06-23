/**
 * 2017-06-04
 * @uses mage.translate.translate()
 * https://github.com/magento/magento2/blob/2.1.7/lib/web/mage/translate.js#L39-L46
 */
define(['Df_Intl/dic', 'mage/translate'], function(dic, $t) {return function(v) {return(
	dic.get(v) || $t(v)
);};});
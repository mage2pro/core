<?php
namespace Df\Checkout;
/**
 * 2021-10-28 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
 * @see \Df\Checkout\Session
 */
abstract class SessionBase extends \Df\Core\Session {
	/**
	 * 2021-10-22
	 * 2021-10-26
	 *	<virtualType name="Magento\Checkout\Model\Session\Storage" type="Magento\Framework\Session\Storage">
	 *		<arguments>
	 *			<argument name="namespace" xsi:type="string">checkout</argument>
	 *		</arguments>
	 *	</virtualType>
	 * https://github.com/magento/magento2/blob/2.4.3-p1/app/code/Magento/Checkout/etc/di.xml#L9-L13
	 * @override
	 * @see \Df\Core\Session::c()
	 * @used-by \Df\Core\Session::__construct()
	 */
	final protected function c():string {return 'Magento\Checkout\Model\Session\Storage';}
}
<?php
namespace Df\Paypal\Plugin\Model\Api;
use Magento\Paypal\Model\Api\Nvp as Sb;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order\Address as OA;
# 2019-10-16
final class Nvp {
	/**
	 * 2019-10-16
	 * "The PayPal payment method on the Magento's checkout page is broken": https://github.com/beverageuniverse/core/issues/5
	 * @see \Magento\Paypal\Model\Api\Nvp::callDoExpressCheckoutPayment()
	 * @see \Magento\Paypal\Model\Express::_placeOrder()
	 */
	function beforeCallDoExpressCheckoutPayment(Sb $sb):void {
		if (self::eligible($a = $sb['address'])) {/** @var OA $a */
			$sb->addData(['address' => $a->getOrder()->getBillingAddress(), 'suppress_shipping' => true]);
			$sb->unsetData('billing_address');
		}
	}

	/**
	 * 2019-10-16
	 * "The PayPal payment method on the Magento's checkout page is broken":
	 * https://github.com/beverageuniverse/core/issues/5
	 * @see \Magento\Paypal\Model\Api\Nvp::callSetExpressCheckout()
	 * @param Sb $sb
	 */
	function beforeCallSetExpressCheckout(Sb $sb):void {
		if (self::eligible($sb['address'])) {
			$sb['suppress_shipping'] = true;
		}
	}

	/**
	 * 2019-10-16
	 * 2019-12-14
	 * $a can be null at least in Magento 2.2.5:
	 * «Call to a member function getEmail() on null in vendor/mage2pro/core/Paypal/Plugin/Model/Api/Nvp.php:43»:
	 * https://github.com/royalwholesalecandy/core/issues/41
	 * @used-by self::beforeCallDoExpressCheckoutPayment()
	 * @used-by self::beforeCallSetExpressCheckout()
	 * @param OA|QA|null $a
	 */
	private static function eligible($a):bool {return $a && df_ends_with($a->getEmail(), '@mage2.pro');}
}
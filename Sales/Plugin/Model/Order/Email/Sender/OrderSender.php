<?php
namespace Df\Sales\Plugin\Model\Order\Email\Sender;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as Sb;
// 2017-07-20
final class OrderSender {
	/**
	 * 2017-07-20
	 * The purpose of this plugin is to detect an order transactional email sending process:
	 * https://mage2.pro/t/4236
	 * «How is a confirmation email sent on an order placement?» https://mage2.pro/t/1542
	 * @see \Magento\Sales\Model\Order\Email\Sender\OrderSender::send()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Sales/Model/Order/Email/Sender/OrderSender.php#L82-L115
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param O $o
	 * @param bool $forceSyncMode [optional]
	 * @return false
	 */
	function aroundSend(Sb $sb, \Closure $f, O $o, $forceSyncMode = false) {
		try {self::$on = true; $f($o, $forceSyncMode);} finally {self::$on = false;}
		return false;
	}

	/**
	 * 2017-07-20
	 * @used-by \Dfe\Moip\CardFormatter::label()
	 * @return bool
	 */
	static function is() {return self::$on;}

	/**
	 * 2017-07-20
	 * @used-by aroundSave()
	 * @used-by is()
	 * @var bool
	 */
	private static $on = false;
}
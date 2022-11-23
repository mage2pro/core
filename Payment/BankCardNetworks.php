<?php
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\File;
# 2017-07-19
final class BankCardNetworks {
	/**
	 * 2017-07-19
	 * @used-by self::url()
	 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
	 * @return string[]
	 */
	static function custom():array {return [self::Hipercard, self::Hiper, self::Elo, self::UnionPayForBraintree];}

	/**
	 * 2017-07-19
	 * All the mine and the core's bank card network logos have the 46x30  dimensions (in pixels).
	 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
	 * @used-by \Dfe\Moip\CardFormatter::label()
	 * @param int|null $w [optional]
	 * @param int|null $h [optional]
	 * @return array(string => int)
	 */
	static function dimensions($w = null, $h = null):array {return array_combine(['width', 'height'],
		(!$w && !$h) ? [46, 30] : (
			!$w ? [round(46 * $h / 30), $h] : (
				!$h ? [$w, round(30 * $w / 46)] : [$w, $h]))
	);}

	/**
	 * 2017-07-19
	 * Note 1.
	 * For now, all the logos has the 46x30 dimensions (in pixels).
	 * In future, the dimensions can be detected programmatically with the following code:
	 * https://github.com/mage2pro/core/blob/2.8.26/Payment/ConfigProvider/GlobalT.php#L63-L72
	 * Note 2.
	 * The function is implemented by analogy with @see \Magento\Payment\Model\CcConfigProvider::getIcons():
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/Model/CcConfigProvider.php#L58-L86
	 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
	 * @param bool|\Closure|mixed $onE [optional]
	 * @throws DFE
	 */
	static function url(string $t, $onE = true):string {return df_try(function() use($t) {return df_asset_url(
		# 2020-02-08
		# 1) It is for the Magento's Braintree payment module.
		# "An icon of the UnionPay bank card network is absent on the frontend Braintree payment form":
		# https://github.com/frugue/site/issues/27
		# 2) The Braintree module uses the `CUP`identifier for the UnionPay bank card network.
		# 3) Magento does not declare the UnionPay bank card network at all, but contains the `un.png` icon.
		# 4) I declare the UnionPay bank card network in the vendor/mage2pro/core/Payment/etc/payment.xml file.
		self::UnionPayForBraintree === $t
		? 'Magento_Payment::images/cc/un.png' : "Df_Payment::i/bank-card/$t.png"
	) ?: df_error("Unable to find a logo image for the «{$t}» bank card network");}, $onE);}

	/**
	 * 2017-07-19
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Payment/view/base/web/images/cc/ae.png
	 */
	const American_Express = 'ae';

	/**
	 * 2017-07-19
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Payment/view/base/web/images/cc/dn.png
	 */
	const Diners_Club = 'dn';
	
	/**
	 * 2017-07-19
	 * @used-by self::custom()
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 */
	const Elo = 'Elo';

	/**
	 * 2017-07-19
	 * @used-by self::custom()
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 */	
	const Hiper = 'Hiper';

	/**
	 * 2017-07-19
	 * @used-by self::custom()
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 */	
	const Hipercard = 'Hipercard';

	/**
	 * 2017-07-19
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Payment/view/base/web/images/cc/mc.png
	 */
	const MasterCard = 'mc';

	/**
	 * 2017-07-19
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.4/app/code/Magento/Payment/view/base/web/images/cc/vi.png
	 */
	const Visa = 'vi';

	/**
	 * 2020-02-20
	 * @used-by self::custom()
	 */
	const UnionPayForBraintree = 'CUP';
}
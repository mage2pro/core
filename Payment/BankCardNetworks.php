<?php
namespace Df\Payment;
use Df\Core\Exception as DFE;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Source as AssetSource;
// 2017-07-19
final class BankCardNetworks {
	/**
	 * 2017-07-19
	 * @used-by url()
	 * @used-by \Df\Payment\ConfigProvider\GlobalT::icons()
	 * @return string[]
	 */
	static function custom() {return [self::Hipercard, self::Hiper, self::Elo];}

	/**
	 * 2017-07-19
	 * Note 1.
	 * For now, all the logos has the 46x30 dimensions (in pixels).
	 * In future, the dimensions can be detected programmatically with the following code:
	 * https://github.com/mage2pro/core/blob/2.8.26/Payment/ConfigProvider/GlobalT.php#L63-L72
	 * Note 2.
	 * The function is implemented by analogy with @see \Magento\Payment\Model\CcConfigProvider::getIcons():
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/Model/CcConfigProvider.php#L58-L86
	 * @param string $type
	 * @param bool|\Closure|mixed $onError [optional]
	 * @return string
	 * @throws DFE
	 */
	static function url($type, $onError = true) {
		/** @var string|null $r */
		$r = dfcf(function($type) {
			$c = df_o(CcConfig::class); /** @var CcConfig $c */
			$src = df_o(AssetSource::class); /** @var AssetSource $src */
			$f = $c->createAsset(
				(in_array($type, self::custom()) ? 'Df_Payment::i/bank-card' : 'Magento_Payment::images/cc')
				."/$type.png"
			); /** @var File $f */
			return !$src->findSource($f) ? null : $f->getUrl();
		}, [$type]);
		return df_try(function() use($r, $type) {return $r ?: df_error(
			"Unable to find a logo image for the «{$type}» bank card network"
		);}, $onError);
	}

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
	 * @used-by custom()
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 */
	const Elo = 'Elo';

	/**
	 * 2017-07-19
	 * @used-by custom()
	 * @used-by \Dfe\Moip\Facade\Card::logoId()
	 */	
	const Hiper = 'Hiper';

	/**
	 * 2017-07-19
	 * @used-by custom()
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
}
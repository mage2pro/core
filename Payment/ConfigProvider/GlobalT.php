<?php
namespace Df\Payment\ConfigProvider;
use Magento\Checkout\Model\ConfigProviderInterface as Sb;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Source as AssetSource;
use Magento\Payment\Model\CcConfig;
// 017-04-26
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class GlobalT implements Sb {
	/**
	 * 2017-04-26
	 * Замечание №1.
	 * Этот метод вызывается не только на странице оформления заказа, но и на странице корзицы.
	 * Однако нам на странице корзины не нужно вычислять настройки наших способов оплаты:
	 * ведь они там не отображаются, а вычисление настрое расходует ресурсы:
	 * в частности, мой модуль Stripe при этом делает 2 запроса к API Stripe.
	 * Поэтому на странице корзины ничего не делаем:
	 * Magento потом всё равно вызовет этот метод повторно на странице оформления заказа.
	 *
	 * Замечание №2.
	 * Обратите внимание, что оформление заказа состоит из нескольких шагов,
	 * но переключение между ними происходит без перезагрузки страницы,
	 * поэтому этот метод вызывается лишь единожды на самом первом шаге
	 * (обычно это шаг выбора адреса и способа доставки).
	 *
	 * Замечание №3.
	 * Наша цель на сегодня — добавить иконки допольнительных платёжных систем.
	 * Делаем это по аналогии с @see \Magento\Payment\Model\CcConfigProvider::getIcons():
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/Model/CcConfigProvider.php#L58-L86
	 * How is the bank card networks bar implemented? https://mage2.pro/t/3853
	 *
	 * Замечание №4.
	 * There is no an ability to set an exact ordering
	 * for the items of a dependency injection's argument of type «array»: https://mage2.pro/t/3855
	 * Но в данном случае нас это не колышет.
	 *
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see Sb::getConfig()
	 * https://github.com/magento/magento2/blob/cf7df72/app/code/Magento/Checkout/Model/ConfigProviderInterface.php#L15-L20
	 * @used-by \Magento\Checkout\Model\CompositeConfigProvider::getConfig()
	 * @return array(string => mixed)
	 */
	function getConfig() {return !df_is_checkout() ? [] : ['payment' => ['ccform' => [
		'icons' => $this->icons()
	]]];}

	/**
	 * 2017-04-26
	 * По аналогии с @see \Magento\Payment\Model\CcConfigProvider::getIcons():
	 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/Model/CcConfigProvider.php#L58-L86
	 * How is the bank card networks bar implemented? https://mage2.pro/t/3853
	 * @used-by getConfig()
	 * @return array(string => mixed)
	 */
	private function icons() {
		/** @var CcConfig $c */
		$c = df_o(CcConfig::class);
		/** @var AssetSource $src */
		$src = df_o(AssetSource::class);
		return df_clean(df_map(function($t) use($c, $src) {/** @var File $f */ return
			!$src->findSource($f = $c->createAsset("Df_Payment::i/bank-card/$t.png")) ? [null, null] : [
				/**
				 * 2017-04-26
				 * Изначально написал здесь универсальный код:
				 * 		['url' => $f->getUrl()] + array_combine(
				 *			['width', 'height'], getimagesize($f->getSourceFile())
				 *		)
				 * Но потом понял, что у меня все иконки платёжных систем единого размера: 46x30
				 * (и в ядре сейчас все иконки платёжных систем такиого же размера),
				 * поэтому решил не расходовать ресурсы на определение этих размеров в реальном времени.
				 */
				$t, ['height' => 30, 'url' => $f->getUrl(), 'width' => 46]
			]
		;}, ['Hipercard', 'Hiper'], [], [], 0, true));
	}
}
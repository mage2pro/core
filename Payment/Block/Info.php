<?php
namespace Df\Payment\Block;
use Df\Payment\Choice;
use Df\Payment\Info\Entry;
use Df\Payment\Info\Report;
use Df\Payment\Method as M;
use Df\Payment\W\Event;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\AbstractBlock as _P;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Payment as OP;
/**
 * 2016-05-06
 * @see \Df\GingerPaymentsBase\Block\Info
 * @see \Df\StripeClone\Block\Info
 * @see \Dfe\AllPay\Block\Info
 * @see \Dfe\AlphaCommerceHub\Block\Info
 * @see \Dfe\Dragonpay\Block\Info
 * @see \Dfe\IPay88\Block\Info
 * @see \Dfe\Moip\Block\Info\Boleto
 * @see \Dfe\MPay24\Block\Info
 * @see \Dfe\Paypal\Block\Info
 * @see \Dfe\Paystation\Block\Info
 * @see \Dfe\PostFinance\Block\Info
 * @see \Dfe\Qiwi\Block\Info
 * @see \Dfe\Robokassa\Block\Info
 * @see \Dfe\SecurePay\Block\Info
 * @see \Dfe\Tinkoff\Block\Info
 * @see \Dfe\TwoCheckout\Block\Info
 * @see \Dfe\YandexKassa\Block\Info
 *
 * @used-by \Df\Payment\Method::getInfoBlockType():
 * 		function getInfoBlockType() {return df_con_hier($this, \Df\Payment\Block\Info::class);}
 * https://github.com/mage2pro/core/blob/2.8.24/Payment/Method.php#L938-L958
 *
 * @used-by \Magento\Payment\Helper\Data::getInfoBlock()
 */
abstract class Info extends _P {
	/**
	 * 2017-08-03
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::getCacheKeyInfo():
	 *		public function getCacheKeyInfo() {
	 * 			return [$this->getNameInLayout()];
	 * 		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L999-L1009
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::getCacheKey():
	 *		$key = $this->getCacheKeyInfo();
	 *		$key = array_values($key);  // ignore array keys
	 *		$key = implode('|', $key);
	 *		$key = sha1($key); // use hashing to hide potentially private data
	 *		return static::CACHE_KEY_PREFIX . $key;
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L1011-L1033
	 * @return string[]
	 */
    function getCacheKeyInfo() {return [
    	df_cts($this), df_cts($this->m()), $this->_pdf, $this->isSecureMode(), dfa_hash($this->iia())
	];}

	/**
	 * 2017-08-03
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @used-by \Magento\Payment\Helper\Data::getInfoBlockHtml():
	 *		$paymentBlock = $this->getInfoBlock($info);
	 *		$paymentBlock
	 *			->setArea(\Magento\Framework\App\Area::AREA_FRONTEND)
	 *			->setIsSecureMode(true);
	 *		$paymentBlock->getMethod()->setStore($storeId);
	 *		$paymentBlockHtml = $paymentBlock->toHtml();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/Helper/Data.php#L198-L226
	 * @return M
	 */
	final function getMethod() {return $this->m();}

	/**
	 * 2017-08-03
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @used-by \Magento\Payment\Helper\Data::getInfoBlock():
	 *		public function getInfoBlock(InfoInterface $info, LayoutInterface $layout = null) {
	 *			$layout = $layout ?: $this->_layout;
	 *			$blockType = $info->getMethodInstance()->getInfoBlockType();
	 *			$block = $layout->createBlock($blockType);
	 *			$block->setInfo($info);
	 *			return $block;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/Helper/Data.php#L182-L196
	 *
	 * 2017-08-24
	 * $i is a @see \Magento\Quote\Model\Quote\Payment on the frontend «multishipping/checkout/overview» page:
	 * this page is shown to the customer just before an order placement.
	 * An example of a «multishipping/checkout/overview» page: https://mage2.pro/t/4403
	 *
	 * @param II|I|OP|QP $i
	 */
	final function setInfo(II $i) {$this->_i = $i;}

	/**
	 * 2017-08-03
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @used-by \Magento\Payment\Helper\Data::getInfoBlockHtml():
	 *		$paymentBlock = $this->getInfoBlock($info);
	 *		$paymentBlock
	 *			->setArea(\Magento\Framework\App\Area::AREA_FRONTEND)
	 *			->setIsSecureMode(true);
	 *		$paymentBlock->getMethod()->setStore($storeId);
	 *		$paymentBlockHtml = $paymentBlock->toHtml();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/Helper/Data.php#L198-L226
	 * @used-by \Magento\Sales\Model\Order\Pdf\AbstractPdf::insertOrder():
	 *		$paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
	 *		$paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
	 *		$payment = explode('{{pdf_row_separator}}', $paymentInfo);
	 *		foreach ($payment as $key => $value) {
	 *			if (strip_tags(trim($value)) == '') {
	 *				unset($payment[$key]);
	 *			}
	 *		}
	 *		reset($payment);
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Sales/Model/Order/Pdf/AbstractPdf.php#L433-L441
	 * 2017-08-18
	 * The method should return $this because it is used in a chain in the
	 * @see \Magento\Sales\Model\Order\Pdf\AbstractPdf::insertOrder() method (see above).
	 * `Invoice PDF priting leads to the «Call to a member function toPdf() on null
	 * in magento/module-sales/Model/Order/Pdf/AbstractPdf.php:428» failure`: https://mage2.pro/t/4336
	 * @param bool $v
	 * @return $this
	 */
	final function setIsSecureMode($v) {$this->_secureMode = $v; return $this;}

	/**
	 * 2017-03-25
	 * 2017-08-09 We can safely mark this method as «final» because this method is magic in the parent class.
	 * https://github.com/mage2pro/core/issues/20
	 * @override
	 * @used-by \Magento\Sales\Model\Order\Pdf\AbstractPdf::insertOrder():
	 *		$paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
	 *		$paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
	 *		$payment = explode('{{pdf_row_separator}}', $paymentInfo);
	 *		foreach ($payment as $key => $value) {
	 *			if (strip_tags(trim($value)) == '') {
	 *				unset($payment[$key]);
	 *			}
	 *		}
	 *		reset($payment);
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Sales/Model/Order/Pdf/AbstractPdf.php#L433-L441
	 * @used-by \Magento\Payment\Block\Info::getChildPdfAsArray():
	 *		public function getChildPdfAsArray() {
	 *			$result = [];
	 *			foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
	 *				if (method_exists($child, 'toPdf') && is_callable([$child, 'toPdf'])) {
	 *					$result[] = $child->toPdf();
	 *				}
	 *			}
	 *			return $result;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/Block/Info.php#L64-L80
	 * @see \Magento\Payment\Block\Info::toPdf()
	 * @return string
	 */
	final function toPdf() {
		try {$this->_pdf = true; $result = $this->toHtml();}
		finally {$this->_pdf = false;}
		return $result;
	}

	/**
	 * 2017-03-25
	 * Замечание №1.
	 * В сценарии формирования блока с платёжной информацией для письма-подтверждения
	 * @see \Magento\Framework\App\State::getAreaCode() возвращает «webapi_rest»,
	 * поэтому будьте осторожны: мы попадаем в _toHtml() в контексте не 2-х областей кода
	 * (витрина и административная часть), а 3-х.
	 * How is a confirmation email sent on an order placement? https://mage2.pro/t/1542
	 * How is the payment information block rendered in an order confirmation email? https://mage2.pro/t/3550
	 * Замечание №2.
	 * Для PDF пока оставляем шаблон без изменения: @see \Magento\Payment\Block\Info::toPdf()
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 * @return string|null
	 */
	final protected function _toHtml() {
		// 2017-08-03
		// Текущие проверки нам нужны, чтобы блок одного модуля не отображался после оплаты другим
		// на странице «checkout success».
		return (
			!($m = $this->m()) instanceof M
			/**
			 * 2017-04-01
			 * Результат вполне может быть абстрактным:
			 * например, если текущий класс — @see \Df\GingerPaymentsBase\Block\Info
			 * @var string $с
			 * @var string|null $s
			 */
			|| !($с = dfpm_c($this, true))
			|| !is_a($m, $с)
		) ? null : (
			$this->_pdf ? $this->rPDF() : (
				df_sales_email_sending() ? $this->rEmail() : (
					df_is_checkout_success() ? $this->rCheckoutSuccess() : (
						df_is_backend() ? $this->rBackend() : (
							df_is_checkout_multishipping() ? $this->rMultishipping() :
								$this->rCustomerAccount()
						)
					)
				)
			)
		);
	}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::bt()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
	 * @used-by \Dfe\AllPay\Block\Info::prepareDic()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @return Choice
	 */
	protected function choice() {return dfp_choice($this->ii());}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
	 * @used-by \Dfe\AllPay\Block\Info::prepareDic()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\PostFinance\Block\Info::prepare()
	 * @used-by \Dfe\Robokassa\Block\Info::prepare()
	 * @return Phrase|string
	 */
	protected function choiceT() {return $this->choice()->title() ?:  __('Not selected yet');}

	/**
	 * 2016-08-09
	 * @used-by si()
	 * @used-by \Dfe\AllPay\Block\Info::prepareDic()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @return Report
	 */
	final protected function dic() {return dfc($this, function() {return new Report;});}

	/**
	 * 2016-07-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::eci()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	protected function e(...$k) {return $this->tm()->responseF(...$k);}

	/**
	 * 2017-03-25
	 * Для меня название метода getIsSecureMode() неинтуитивно, и я всё время путаюсь с его значением.
	 * Поэтому объявил свой идентичный метод.
	 * @used-by siEx()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @param mixed[] ...$args [optional]
	 * @return bool|mixed
	 */
	final protected function extended(...$args) {return df_b($args, !$this->isSecureMode());}

	/**
	 * 2016-05-21   
	 * 2017-08-24
	 * The result is a @see \Magento\Quote\Model\Quote\Payment 
	 * on the frontend «multishipping/checkout/overview» page:
	 * this page is shown to the customer just before an order placement.
	 * An example of a «multishipping/checkout/overview» page: https://mage2.pro/t/4403
	 * @used-by getCacheKeyInfo()
	 * @used-by iia()
	 * @used-by isTest()
	 * @used-by m()
	 * @used-by option()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::btInstructions()
	 * @used-by \Dfe\Square\Block\Info::prepare()
	 * @param string|null $k [optional]
	 * @return II|I|OP|QP|mixed
	 */
	final protected function ii($k = null) {return dfak(
		$this->_i ?: df_checkout_session()->getLastRealOrder()->getPayment(), $k
	);}

	/**
	 * 2016-05-21
	 * @used-by \Dfe\TwoCheckout\Block\Info::cardNumber()
	 * @used-by \Dfe\TwoCheckout\Block\Info::prepare()
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	final protected function iia(...$keys) {$i = $this->ii(); return
		!$keys ? $i->getAdditionalInformation() : (
			1 === count($keys)
			? $i->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($i->getAdditionalInformation(), $keys)
		)
	;}

	/**
	 * 2016-05-23
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @used-by \Df\StripeClone\Block\Info::cardData()
	 * @used-by \Dfe\TwoCheckout\Block\Info::prepare()
	 * @param bool|mixed $t [optional]
	 * @param bool|mixed $f [optional]
	 * @return bool|mixed
	 */
	final protected function isTest($t = true, $f = false) {return dfc($this, function() {return
		dfp_is_test($this->ii());}) ? $t : $f
	;}

	/**
	 * 2017-02-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by rCheckoutSuccess()
	 * @used-by s()
	 * @used-by siID()
	 * @used-by titleB()
	 * @used-by tm()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::option()
	 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return M
	 */
	protected function m() {return dfpm($this->ii());}

	/**
	 * 2017-03-29
	 * @used-by rCheckoutSuccess()
	 * @see \Df\GingerPaymentsBase\Block\Info::msgCheckoutSuccess()
	 * @see \Dfe\Moip\Block\Info\Boleto::msgCheckoutSuccess()
	 * @return string|null
	 */
	protected function msgCheckoutSuccess() {return 'Not implemented.';}

	/**
	 * 2017-03-29
	 * @used-by rUnconfirmed()
	 * @see \Df\GingerPaymentsBase\Block\Info::msgUnconfirmed()
	 * @return string|null
	 */
	protected function msgUnconfirmed() {return df_tag('div', 'df-unconfirmed-text', __(
		'The payment is not yet confirmed by %1.', $this->titleB()
	));}

	/**
	 * 2016-11-17
	 * Класс вполне может быть работоспособным и без этого метода:
	 * тогда блок с информацией о платеже будет содержать только название способа оплаты
	 * и вид режима платежа: тестовый или промышленный.
	 * Однако я специально сделал метод абстрактным: чтобы:
	 * 1) разработчики платёжных модулей (я) не забывали,
	 * что этот метод — главный в классе, и именно его им нужно переопределять.
	 * 2) заставить разработчиков платёжных модулей (меня)
	 * не лениться отображать дополнительную инфомацию о платеже.
	 *
	 * 2016-11-29
	 * Почему-то текущая dev-версия Magento 2 некорректно компилирует это класс
	 * при объявлении метода prepare() абстрактным:
	 * «Fatal error: Class Df\Payment\Block\Info\Interceptor contains 1 abstract method
	 * and must therefore be declared abstract or implement the remaining methods
	 * (Df\Payment\Block\Info::prepare)»
	 * Поэтому был вынужден убрать «abstract».
	 *
	 * @used-by \Df\Payment\Block\Info::prepareToRendering()
	 * @see \Df\GingerPaymentsBase\Block\Info::prepare()
	 * @see \Df\StripeClone\Block\Info::prepare()
	 * @see \Dfe\AllPay\Block\Info::prepare()
	 * @see \Dfe\Dragonpay\Block\Info::prepare()
	 * @see \Dfe\IPay88\Block\Info::prepare()
	 * @see \Dfe\Moip\Block\Info\Boleto::prepare()
	 * @see \Dfe\MPay24\Block\Info::prepare()
	 * @see \Dfe\Paypal\Block\Info::prepare()
	 * @see \Dfe\Paystation\Block\Info::prepare()
	 * @see \Dfe\PostFinance\Block\Info::prepare()
	 * @see \Dfe\Qiwi\Block\Info::prepare()
	 * @see \Dfe\Robokassa\Block\Info::prepare()
	 * @see \Dfe\SecurePay\Block\Info::prepare()
	 * @see \Dfe\Square\Block\Info::prepare()
	 * @see \Dfe\Tinkoff\Block\Info::prepare()
	 * @see \Dfe\TwoCheckout\Block\Info::prepare()
	 * @see \Dfe\YandexKassa\Block\Info::prepare()
	 */
	protected function prepare() {df_abstract($this);}

	/**
	 * 2016-08-09
	 * @used-by \Df\Payment\Block\Info::getSpecificInformation()
	 * @see \Dfe\AllPay\Block\Info::prepareDic()
	 * @see \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 */
	protected function prepareDic() {}

	/**
	 * 2016-08-13
	 * Сюда мы попадаем в 2 случаях:
	 * 1) Платёж находится в состоянии «Review» (случай модуля Stripe).
	 * 2) ПС работает с перенаправлением покупателя на свою страницу.
	 * Покупатель был туда перенаправлен, однако ПС ещё не прислала оповещение о платеже
	 * (и способе оплаты). Т.е. покупатель ещё ничего не оплатил, и, возможно,
	 * просто закрыл страницу оплаты и уже ничего не оплатит. (случай модуля allPay).
	 * 2016-11-17
	 * Этот метод инициализирирует информацию о ещё не подтверждённом платёжной системой
	 * или находящемся на модерации (review) в интернет-магазине платеже.
	 * @used-by \Df\Payment\Block\Info::prepareToRendering()
	 * @see \Df\GingerPaymentsBase\Block\Info::prepareUnconfirmed()
	 * @see \Dfe\AllPay\Block\Info\BankCard::prepareUnconfirmed()
	 * @see \Dfe\AllPay\Block\Info\Offline::prepareUnconfirmed()
	 */
	protected function prepareUnconfirmed() {$this->si('State', __('Review'));}

	/**
	 * 2017-08-04
	 * @used-by _toHtml()
	 * @return string
	 */
	protected function rBackend() {
		$this->prepareToRendering();
		// 2017-03-29 https://github.com/mage2pro/core/blob/2.4.9/Payment/view/adminhtml/web/main.less#L6
		return df_tag('div', 'df-payment-info',
			$this->m()->getTitle() . $this->rUnconfirmed() . $this->rTable()
		);
	}
	
	/**
	 * 2017-08-04
	 * @used-by _toHtml()
	 * @see \Dfe\Moip\Block\Info\Boleto::rCustomerAccount()
	 * @return string
	 */
	protected function rCustomerAccount() {
		$this->prepareToRendering();
		// 2017-03-29 https://github.com/mage2pro/core/blob/2.4.9/Core/view/base/web/main.less#L41
		return df_tag('div', 'df-payment-info',
			df_tag('dl', 'payment-method', df_cc_n(
				df_tag('dt', 'title', $this->m()->getTitle())
				.df_tag('dd', 'content', $this->rUnconfirmed() . $this->rTable())
			))
		);
	}

	/**
	 * 2017-08-04
	 * @used-by _toHtml()
	 * @return string
	 */
	protected function rEmail() {return $this->rCustomerAccount();}

	/**
	 * 2017-08-02
	 * I have implemented in by analogi with the standard PHP rendering implementation.
	 * The standard PDF template is the same for the frontend and the backend parts:
	 * 		<?= $block->escapeHtml($block->getMethod()->getTitle()) ?>{{pdf_row_separator}}
	 *			<?php if ($specificInfo = $block->getSpecificInformation()):?>
	 *				<?php foreach ($specificInfo as $label => $value):?>
	 *					<?= $block->escapeHtml($label) ?>:
	 *					<?= $block->escapeHtml(implode(' ', $block->getValueAsArray($value))) ?>
	 *					{{pdf_row_separator}}
	 *				<?php endforeach; ?>
	 *			<?php endif;?>
	 * 		<?= $block->escapeHtml(implode('{{pdf_row_separator}}', $block->getChildPdfAsArray())) ?>
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/view/adminhtml/templates/info/pdf/default.phtml#L15
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Payment/view/frontend/templates/info/pdf/default.phtml#L15
	 * @see \Magento\Payment\Block\Info::toPdf()
	 * @used-by _toHtml()
	 * @return string
	 */
	protected function rPDF() {
		$this->prepareToRendering();
		return implode('{{pdf_row_separator}}', df_clean(dfa_flatten([
			$this->m()->getTitle()
			,df_map($this->dic(), function(Entry $e) {return
				!$e->name() ? $e->value() : "{$e->name()}: {$e->value()}"
			;})
		])));
	}

	/**
	 * 2017-02-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::bt()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
	 * @param string|null $k [optional]
	 * @return \Df\Payment\Settings
	 */
	protected function s($k = null) {return $this->m()->s($k);}

	/**
	 * 2016-11-17
	 * Не вызываем здесь @see __(),
	 * потому что словарь ещё будет меняться, в частности, методом @see prepareDic()
	 * @see getSpecificInformation()
	 * Ключи потом будут автоматически переведены методом @see \Df\Payment\Info\Entry::nameT()
	 * Значения переведены не будут!
	 * @used-by siEx()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
	 * @used-by \Dfe\PostFinance\Block\Info::prepare()
	 * @used-by \Dfe\Robokassa\Block\Info::prepare()
	 * @used-by \Dfe\Square\Block\Info::prepare()
	 * @param string|Phrase|null|array(string => string) $k
	 * @param string|null $v [optional]
	 */
	final protected function si($k, $v = null) {
		is_array($k)
		// 2016-11-17
		// К сожалению, нельзя использовать [$this, __FUNCTION__], потому что метод si() — protected.
		// https://3v4l.org/64N3q
		? df_map_k(function($k, $v) {return $this->si($k, $v);}, $k)
		// 2017-02-19
		// Отныне пустые строки выводить не будем.
		: (df_nes($v) ? null : $this->dic()->add($k, $v));
	}

	/**
	 * 2016-11-17
	 * @used-by siID()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepare()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\AlphaCommerceHub\Block\Info::prepare()
	 * @used-by \Dfe\IPay88\Block\Info::prepare()
	 * @used-by \Dfe\PostFinance\Block\Info::prepare()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @used-by \Dfe\TwoCheckout\Block\Info::prepare()
	 * @param string|Phrase|null|array(string => string) $k
	 * @param string|null $v [optional]
	 */
	final protected function siEx($k, $v = null) {
		if ($this->extended()) {
			$this->si($k, $v);
		}
	}

	/**
	 * 2017-03-29
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 */
	final protected function siID() {return $this->siEx(
		"{$this->titleB()} ID", $this->m()->tidFormat($this->tm()->tReq(), true)
	);}

	/**
	 * 2016-07-13
	 * @used-by _toHtml()
	 * @see \Dfe\TwoCheckout\Block\Info::testModeLabel()
	 * @return string
	 */
	protected function testModeLabel() {return 'Test';}

	/**
	 * 2017-03-29
	 * @used-by confirmed()
	 * @used-by e()
	 * @used-by siID()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::option()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::res0()
	 * @used-by \Df\StripeClone\Block\Info::cardData()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @used-by \Dfe\Moip\Block\Info\Boleto::prepare()
	 * @used-by \Dfe\Square\Block\Info::prepare()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
	 * @return \Df\Payment\TM
	 */
	final protected function tm() {return df_tm($this->m());}

	/**
	 * 2017-03-29
	 * `How to preserve the «checkout success» page after reloading in browser (for the testing purposes)?`
	 * https://mage2.pro/t/3566
	 * 2017-04-01
	 * Проверки нам нужны, чтобы блок одного модуля не отображался после оплаты другим.
	 * https://github.com/mage2pro/core/blob/2.4.9/Checkout/view/frontend/web/success.less#L5
	 * @used-by _toHtml()
	 * @return string|null
	 */
	private function rCheckoutSuccess() {/** @var M|IM $m */ return
		!($s = $this->msgCheckoutSuccess()) ? null : df_tag('div', 'df-checkout-success', $s)
	;}		

	/**
	 * 2016-08-29
	 * В реализации @see \Magento\Payment\Block\Info::getIsSecureMode() меня не устраивает такой код:
	 *	$store = $method->getStore();
	 *	if (!$store) {
	 *		return false;
	 *	}
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/Block/Info.php#L132-L135
	 * В моём случае на витрине $method->getStore() возвращает null (не разбирался, почему)
	 * и тогда, соответственно, @see \Magento\Payment\Block\Info::getIsSecureMode() возвращает false,
	 * т.е. система считает, что мы находимся в административной части, что неверно.
	 *
	 * 2017-03-25
	 * @see _secureMode у нас равно true только в контексте писем и PDF:
	 * How is the setIsSecureMode() magic method used for a payment information block?
	 * https://mage2.pro/t/3551
	 *
	 * @used-by extended()
	 * @used-by getCacheKeyInfo()
	 * @return bool
	 */
	private function isSecureMode() {return !df_is_backend() || $this->_secureMode;}

	/**
	 * 2017-08-04
	 * @used-by rBackend()
	 * @used-by rCheckoutSuccess()
	 * @used-by rCustomerAccount()
	 * @used-by rEmail()
	 * @used-by rPDF()
	 */
	private function prepareToRendering() {
		$this->tm()->confirmed() ? $this->prepare() : $this->prepareUnconfirmed();
		if ($this->isTest()) {
			$this->si('Mode', __($this->testModeLabel()));
		}
		$this->prepareDic();
		$this->dic()->sort();
	}

	/**
	 * 2017-08-24
	 * @used-by _toHtml()
	 * @return Phrase
	 */
	private function rMultishipping() {return $this->m()->getTitle();}

	/**
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rTable() {return !$this->dic()->count() ? '' : df_tag('table',
		!df_is_backend() ? 'data table' : df_cc_s(
			'data-table admin__table-secondary df-payment-info', $this->ii('method')
		)
		,df_cc_n(df_map($this->dic(), function(Entry $e) {return
			df_tag('tr', [],
				!$e->name() ? df_tag('td', ['colspan' => 2], $e->value()) :
					// 2017-07-19
					// The previous code for the second argument was: $b ? [] : ['scope' => 'row'].
					// It was ported from the core.
					// But it looks like `scope=row` is not used anywhere.
					df_tag('th', [], $e->name()) . df_tag('td', [], $e->value())
			)
		;}))
	);}
	
	/**
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rUnconfirmed() {return $this->tm()->confirmed() ? '' : df_tag(
		'div', 'df-unconfirmed', $this->msgUnconfirmed()
	);}

	/**
	 * 2017-01-13
	 * @used-by siID()
	 * @used-by msgUnconfirmed()
	 * @return string
	 */
	private function titleB() {return $this->m()->titleB();}

	/**
	 * 2017-08-03    
	 * 2017-08-24
	 * $_i is a @see \Magento\Quote\Model\Quote\Payment on the frontend «multishipping/checkout/overview» page:
	 * this page is shown to the customer just before an order placement.
	 * An example of a «multishipping/checkout/overview» page: https://mage2.pro/t/4403
	 * @used-by ii()
	 * @used-by setInfo()
	 * @var II|I|OP|QP
	 */
	private $_i;

	/**
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @used-by getCacheKeyInfo()
	 * @used-by toPdf()
	 * @var bool|null
	 */
	private $_pdf;

	/**
	 * 2017-08-03
	 * @used-by isSecureMode()
	 * @used-by setIsSecureMode()
	 * @var bool|null
	 */
	private $_secureMode;
}
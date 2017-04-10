<?php
namespace Df\Payment\Block;
use Df\Payment\Info\Dictionary;
use Df\Payment\Method as M;
use Df\Payment\W\Event;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-05-06
 * По аналогии с @see \Magento\Braintree\Block\Info
 * https://github.com/magento/magento2/blob/135f967/app/code/Magento/Braintree/Block/Info.php
 * https://mage2.pro/t/898/3
 *
 * 2016-08-29
 * Класс @see \Magento\Payment\Block\ConfigurableInfo присутствует уже в Magento 2.0.0:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Payment/Block/ConfigurableInfo.php
 * Поэтому мы можем от него наследоваться без боязни сбоев.
 *
 * 2017-02-18
 * @see \Df\GingerPaymentsBase\Block\Info
 * @see \Df\StripeClone\Block\Info
 * @see \Dfe\AllPay\Block\Info
 * @see \Dfe\IPay88\Block\Info
 * @see \Dfe\Paystation\Block\Info
 * @see \Dfe\Robokassa\Block\Info
 * @see \Dfe\SecurePay\Block\Info
 * @see \Dfe\Square\Block\Info
 * @see \Dfe\TwoCheckout\Block\Info
 */
abstract class Info extends \Magento\Payment\Block\ConfigurableInfo {
	/**
	 * 2016-11-17
	 * Класс вполне может быть работоспособным и без этого метода:
	 * тогда блок с информацией о платеже будет содержать только название способа оплаты
	 * и вид режима платежа: тестовый или промышленный.
	 * Однако я специально сделал метод абтрактным: чтобы:
	 * 1) разработчики платёжных модулей (я) не забывали,
	 * что этот метод — главный в классе, и именно его им нужно переопределять.
	 * 2) заставить разработчиков платёжных модулей (меня)
	 * не лениться отображать дополнительную инфомацию о платеже.
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 *
	 * 2016-11-29
	 * Почему-то текущая dev-версия Magento 2 некорректно компилирует это класс
	 * при объявлении метода prepare() абстрактным:
	 * «Fatal error: Class Df\Payment\Block\Info\Interceptor contains 1 abstract method
	 * and must therefore be declared abstract or implement the remaining methods
	 * (Df\Payment\Block\Info::prepare)»
	 * Поэтому был вынужден убрать «abstract».
	 *
	 * @see \Df\GingerPaymentsBase\Block\Info::prepare()
	 * @see \Df\StripeClone\Block\Info::prepare()
	 * @see \Dfe\AllPay\Block\Info::prepare()
	 * @see \Dfe\IPay88\Block\Info::prepare()
	 * @see \Dfe\Paystation\Block\Info::prepare()
	 * @see \Dfe\Robokassa\Block\Info::prepare()
	 * @see \Dfe\SecurePay\Block\Info::prepare()
	 * @see \Dfe\Square\Block\Info::prepare()
	 * @see \Dfe\TwoCheckout\Block\Info::prepare()
	 */
	protected function prepare() {df_abstract($this);}

	/**
	 * 2016-05-21
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::escapeHtml()
	 * @param array|string $data
	 * @param null $allowedTags
	 * @return array|string
	 */
	function escapeHtml($data, $allowedTags = null) {return $data;}

	/**
	 * 2017-03-29
	 * Это перекрытие позволяет нам использовать этот блок на странице «checkout success».
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @override
	 * @see \Magento\Payment\Block\Info::getInfo()
	 * @used-by ii()
	 * @used-by \Magento\Payment\Block\Info::getMethod()
	 * @return OP
	 */
	function getInfo() { return dfc($this, function() {return $this->getData('info') ?:
		df_checkout_session()->getLastRealOrder()->getPayment()
	;});}

	/**
	 * 2016-08-29
	 * В родительской реализации меня не устраивает такой код:
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
	 * !!$this->_getData('is_secure_mode') у нас равно true только в контексте писем и PDF:
	 * How is the setIsSecureMode() magic method used for a payment information block?
	 * https://mage2.pro/t/3551
	 *
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @override
	 * @see \Magento\Payment\Block\Info::getIsSecureMode()
	 * @used-by extended()
	 * @used-by \Magento\Payment\Block\ConfigurableInfo::_prepareSpecificInformation()
	 * @return bool
	 */
	function getIsSecureMode() {return !df_is_backend() || $this->_getData('is_secure_mode');}

	/**
	 * 2016-07-19
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @return array(string => string)
	 */
	function getSpecificInformation() {return dfc($this, function() {
		$this->dic()->addA(parent::getSpecificInformation());
		$this->prepareDic();
		return $this->dic()->get();
	});}

	/**
	 * 2016-05-23
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @used-by https://github.com/mage2pro/2checkout/blob/1.0.4/view/frontend/templates/info.phtml#L5
	 * @used-by \Dfe\TwoCheckout\Block\Info::_prepareSpecificInformation()
	 * @param bool|mixed $t [optional]
	 * @param bool|mixed $f [optional]
	 * @return bool|mixed
	 */
	function isTest($t = true, $f = false) {return dfc($this, function() {return
		dfp_is_test($this->ii());}) ? $t : $f
	;}

	/**
	 * 2016-07-13
	 * 2017-01-13
	 * При вызове из административной части этот метод возвращает заголовок на основе
	 * @see \Df\Payment\Method::titleBackendS()
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @return string
	 */
	function title() {return df_cc_s(
		$this->escapeHtml($this->m()->getTitle())
		,$this->isTest(sprintf("(%s)", __($this->testModeLabelLong())), null)
	);}

	/**
	 * 2017-03-25
	 * @final Unable to use the PHP «final» keyword because of the M2 code generation.
	 * @override
	 * @see \Magento\Payment\Block\Info::toPdf()
	 * @return string
	 */
	function toPdf() {
		try {$this->_pdf = true; $result = parent::toPdf();}
		finally {$this->_pdf = false;}
		return $result;
	}

	/**
	 * 2016-11-17
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::_prepareSpecificInformation()
	 * @used-by \Magento\Payment\Block\Info::getSpecificInformation()
	 * @param DataObject|null $dto
	 * @return DataObject
	 */
	final protected function _prepareSpecificInformation($dto = null) {
		parent::_prepareSpecificInformation($dto);
		$this->confirmed() ? $this->prepare() : $this->prepareUnconfirmed();
		/** @see \Df\Payment\Method::remindTestMode() */
		$this->markTestMode();
		return $this->_paymentSpecificInformation;
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
	 * @see \Magento\Framework\View\Element\Template::_toHtml()
	 * @used-by \Magento\Framework\View\Element\AbstractBlock::toHtml()
	 * @return string
	 */
	final protected function _toHtml() {return $this->_pdf ? parent::_toHtml() : (
		df_is_checkout_success() ? $this->checkoutSuccess() :
			// 2017-03-29
			// https://github.com/mage2pro/core/blob/2.4.9/Core/view/base/web/main.less#L41
			// https://github.com/mage2pro/core/blob/2.4.9/Payment/view/adminhtml/web/main.less#L6
			df_tag('div', 'df-payment-info',
				df_is_backend()
					? $this->getMethod()->getTitle() . $this->rUnconfirmed() . $this->rTable()
					: df_tag('dl', 'payment-method', df_cc_n(
						df_tag('dt', 'title', $this->getMethod()->getTitle())
						.df_tag('dd', 'content', $this->rUnconfirmed() . $this->rTable())
					))
			) . $this->getChildHtml()
	);}

	/**
	 * 2017-04-01
	 * Проверки нам нужны, чтобы блок одного модуля не отображался после оплаты другим.
	 * https://github.com/mage2pro/core/blob/2.4.9/Checkout/view/frontend/web/success.less#L5
	 * 2used-by _toHtml()
	 * @return string|null
	 */
	private function checkoutSuccess() {/** @var M|IM $m */ return
		!($m = $this->m()) instanceof M
		/**
		 * 2017-04-01
		 * Результат вполне может быть абстрактным:
		 * например, если текущий класс — @see \Df\GingerPaymentsBase\Block\Info
		 * @var string $с
		 * @var string|null $s
		 */
		|| !($с = dfpm_c($this, true)) || !is_a($m, $с) || (!($s = $this->msgCheckoutSuccess()))
			? null : df_tag('div', 'df-checkout-success', $s)
	;}

	/**
	 * 2016-08-09
	 * @used-by getSpecificInformation()
	 * @used-by \Dfe\AllPay\Block\Info::prepareDic()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @return Dictionary
	 */
	final protected function dic() {return dfc($this, function() {return new Dictionary;});}

	/**
	 * 2016-07-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::allpayAuthCode()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::custom()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::eci()
	 * @used-by \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
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
	final protected function extended(...$args) {return df_b($args, !$this->getIsSecureMode());}

	/**
	 * 2016-05-06
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::getLabel()
	 * @used-by \Magento\Payment\Block\ConfigurableInfo::setDataToTransfer()
	 * @param string $field
	 * @return Phrase
	 */
	final protected function getLabel($field) {return __($field);}

	/**
	 * 2016-05-21
	 * @used-by iia()
	 * @used-by isTest()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::btInstructions()
	 * @param string|null $k [optional]
	 * @return II|I|OP|mixed
	 */
	final protected function ii($k = null) {return dfak($this->getInfo(), $k);}

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
	 * 2017-02-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by checkoutSuccess()
	 * @used-by s()
	 * @used-by siID()
	 * @used-by title()
	 * @used-by titleB()
	 * @used-by tm()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::option()
	 * @return M
	 */
	protected function m() {return $this->getMethod();}

	/** 2016-07-13 */
	final protected function markTestMode() {
		!$this->isTest() ?: $this->si('Mode', __($this->testModeLabel()))
	;}

	/**
	 * 2017-03-29
	 * @used-by checkoutSuccess()
	 * @see \Df\GingerPaymentsBase\Block\Info::msgCheckoutSuccess()
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
	 * 2016-08-09
	 * @used-by \Df\Payment\Block\Info::getSpecificInformation()
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
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @see \Df\GingerPaymentsBase\Block\Info::prepareUnconfirmed()
	 * @see \Dfe\AllPay\Block\Info::prepareUnconfirmed()
	 */
	protected function prepareUnconfirmed() {$this->si('State', __('Review'));}

	/**
	 * 2017-02-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Block\Info::bt()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepareCommon()
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
	 * @param string|array(string => string) $k
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
		: (df_nes($v) ? null : $this->_paymentSpecificInformation[$k] = $v);
	}

	/**
	 * 2016-11-17
	 * @used-by siID()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::prepare()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info::prepare()
	 * @used-by \Dfe\SecurePay\Block\Info::prepare()
	 * @used-by \Dfe\TwoCheckout\Block\Info::prepare()
	 * @param string|array(string => string) $k
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
	 * @used-by markTestMode()
	 * @see \Dfe\TwoCheckout\Block\Info::testModeLabel()
	 * @return string
	 */
	protected function testModeLabel() {return 'Test';}

	/**
	 * 2016-07-13
	 * @used-by title()
	 * @see \Dfe\TwoCheckout\Block\Info::testModeLabelLong()
	 * @return string
	 */
	protected function testModeLabelLong() {return 'Test Mode';}

	/**
	 * 2017-03-29
	 * @used-by confirmed()
	 * @used-by e()
	 * @used-by siID()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::option()
	 * @used-by \Df\GingerPaymentsBase\Block\Info::res0()
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Dfe\AllPay\Block\Info\Offline::custom()
	 * @return \Df\Payment\TM
	 */
	final protected function tm() {return df_tm($this->m());}

	/**
	 * 2017-03-25
	 * @used-by _prepareSpecificInformation()
	 * @return bool
	 */
	private function confirmed() {return dfc($this, function() {return $this->tm()->confirmed();});}

	/**
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rTable() {return !($info = $this->getSpecificInformation()) ? '' : df_tag('table',
		!($b = df_is_backend()) ? 'data table' : df_cc_s(
			'data-table admin__table-secondary df-payment-info', $this->ii('method')
		)
		,($b ? '' : df_tag('caption', 'table-caption', $this->getMethod()->getTitle()))
		.df_cc_n(df_map_k($info, function($l, $v) use ($b) {return
			df_tag('tr', [], df_cc_n(
				df_tag('th', $b ? [] : ['scope' => 'row'], $l)
				,df_tag('td', [], nl2br(df_cc_n($this->getValueAsArray($v, true))))
			))
		;}))
	);}

	/**
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @return string
	 */
	private function rUnconfirmed() {return $this->confirmed() ? '' : df_tag(
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
	 * 2017-03-25
	 * @used-by _toHtml()
	 * @used-by toPdf()
	 * @var bool
	 */
	private $_pdf = false;
}
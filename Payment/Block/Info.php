<?php
namespace Df\Payment\Block;
use Df\Payment\Info\Dictionary;
use Df\Payment\Method;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
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
 * @see \Df\PaypalClone\BlockInfo  
 * @see \Df\StripeClone\Block\Info
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
	 */
	protected function prepare() {df_abstract($this);}

	/**
	 * 2016-05-21
	 * @override
	 * @see \Magento\Framework\View\Element\AbstractBlock::escapeHtml()
	 * @param array|string $data
	 * @param null $allowedTags
	 * @return array|string
	 */
	function escapeHtml($data, $allowedTags = null) {return $data;}

	/**
	 * 2016-08-29
	 * В родительской реализации меня не устраивает такой код:
		$store = $method->getStore();
		if (!$store) {
			return false;
		}
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Payment/Block/Info.php#L132-L135
	 * В моём случае на витрине $method->getStore() возвращает null (не разбирался, почему)
	 * и тогда, соответственно, @see \Magento\Payment\Block\Info::getIsSecureMode() возвразает false,
	 * т.е. система считает, что мы находимся в административной части, что неверно.
	 *
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::getIsSecureMode()
	 * @return bool
	 */
	function getIsSecureMode() {return !df_is_backend();}

	/**
	 * 2016-05-23
	 * @override
	 * @see \Magento\Framework\View\Element\Template::getTemplate()
	 * @see \Magento\Payment\Block\Info::$_template
	 * @return string
	 */
	function getTemplate() {
		/** @var string $pr */
		$pr = parent::getTemplate();
		return $this->isBackend() && 'Magento_Payment::info/default.phtml' === $pr
			? 'Df_Payment::info.phtml' : $pr
		;
	}

	/**
	 * 2016-07-19
	 * @return array(string => string)
	 */
	function getSpecificInformation() {return dfc($this, function() {
		/**
		 * 2016-08-09
		 * К сожалению, мы не можем делать нецелые веса:
		 * ttp://php.net/manual/function.usort.php
		 * «Returning non-integer values from the comparison function, such as float,
		 * will result in an internal cast to integer of the callback's return value.
		 * So values such as 0.99 and 0.1 will both be cast to an integer value of 0,
		 * which will compare such values as equal.»
		 * Нецелые веса позволили бы нам гарантированно запихнуть
		 * безвесовые записи между весовыми, но увы...
		 */
		$this->dic()->addA(parent::getSpecificInformation());
		$this->prepareDic();
		return $this->dic()->get();
	});}

	/**
	 * 2016-05-21
	 * @used-by vendor/mage2pro/core/Payment/view/adminhtml/templates/info/default.phtml
	 * @param string|null $k [optional]
	 * @return II|I|OP|mixed
	 */
	function ii($k = null) {return dfak($this->getInfo(), $k);}

	/**
	 * 2016-05-23
	 * @used-by https://github.com/mage2pro/2checkout/blob/1.0.4/view/frontend/templates/info.phtml#L5
	 * @used-by \Dfe\TwoCheckout\Block\Info::_prepareSpecificInformation()
	 * @param bool|mixed $t [optional]
	 * @param bool|mixed $f [optional]
	 * @return bool|mixed
	 */
	function isTest($t = true, $f = false) {return
		dfc($this, function() {return dfp_is_test($this->ii());}) ? $t : $f
	;}

	/**
	 * 2016-07-13
	 * 2017-01-13
	 * При вызове из административной части этот метод возвращает заголовок на основе
	 * @see \Df\Payment\Method::titleBackendS()
	 * @return string
	 */
	function title() {return df_cc_s(
		$this->escapeHtml($this->m()->getTitle())
		,$this->isTest(sprintf("(%s)", __($this->testModeLabelLong())), null)
	);}

	/**
	 * 2016-11-17
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::_prepareSpecificInformation()
	 * @used-by \Magento\Payment\Block\Info::getSpecificInformation()
	 * @param DataObject|null $transport
	 * @return DataObject
	 */
	final protected function _prepareSpecificInformation($transport = null) {
		parent::_prepareSpecificInformation($transport);
		$this->isWait() ? $this->siWait() : $this->prepare();
		/** @see \Df\Payment\Method::remindTestMode() */
		$this->markTestMode();
		return $this->_paymentSpecificInformation;
	}

	/**
	 * 2016-08-09
	 * @return Dictionary
	 */
	protected function dic() {return dfc($this, function() {return df_create(Dictionary::class);});}

	/**
	 * 2016-05-06
	 * @override
	 * @see \Magento\Payment\Block\ConfigurableInfo::getLabel()
	 * @used-by \Magento\Payment\Block\ConfigurableInfo::setDataToTransfer()
	 * @param string $field
	 * @return Phrase
	 */
	protected function getLabel($field) {return __($field);}

	/**
	 * 2016-05-21
	 * @param string[] ...$keys
	 * @return mixed|array(string => mixed)
	 */
	protected function iia(...$keys) {return
		!$keys ? $this->ii()->getAdditionalInformation() : (
			1 === count($keys)
			? $this->ii()->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($this->ii()->getAdditionalInformation(), $keys)
		)
	;}

	/**
	 * 2016-08-20
	 * @return bool
	 */
	protected function isBackend() {return df_is_backend();}

	/**
	 * 2016-08-20
	 * @return bool
	 */
	protected function isFrontend() {return !df_is_backend();}

	/**
	 * 2016-11-17
	 * Этот метод должен вернуть true, если реальной информации о платеже пока нет.
	 * Такое возможно в 2 случаях:
	 *
	 * СЛУЧАЙ 1) Платёж либо находится в состоянии «Review» (случай модулей Stripe и Omise).
	 * В этом случае @see transF() возвращает null, хотя покупатель уже заказ оплатил.
	 * Платёж находится на модерации.
	 *
	 * СЛУЧАЙ 2) Модуль работает с перенаправлением покупателя на страницу платёжной системы,
	 * покупатель был туда перенаправлен, однако платёжная система ещё не прислала
	 * оповещение о платеже (и способе оплаты).
	 * Т.е. покупатель ещё ничего не оплатил,  и, возможно, просто закрыл страницу оплаты
	 * и уже ничего не оплатит (случай модуля allPay).
	 * В этом случае метод @see isWait() перекрыт методом @see \Df\PaypalClone\BlockInfo::isWait()
	 * Кстати, в этом случае @see transF() возвращает объект (не null),
	 * потому что транзакция создается перед перенаправлением покупателя.
	 *
	 * @see \Df\PaypalClone\BlockInfo::isWait()
	 * @return bool
	 */
	protected function isWait() {return !$this->transF();}

	/**
	 * 2017-02-18
	 * @final
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @return Method 
	 */
	protected function m() {return $this->getMethod();}

	/** 2016-07-13 */
	protected function markTestMode() {
		!$this->isTest() ?: $this->si('Mode', __($this->testModeLabel()))
	;}

	/**
	 * 2016-08-09
	 * @see \Dfe\AllPay\Block\Info\BankCard::prepareDic()
	 * @used-by \Df\Payment\Block\Info::getSpecificInformation()
	 * @return void
	 */
	protected function prepareDic() {}

	/**
	 * 2016-11-17
	 * Не вызываем здесь @see __(),
	 * потому что словарь ещё будет меняться, в частности, методом @see prepareDic()
	 * @see getSpecificInformation()
	 * Ключи потом будут автоматически переведены методом @see \Df\Payment\Info\Entry::nameT()
	 * Значения переведены не будут!
	 * @used-by siB()
	 * @used-by siF()
	 * @param string|array(string => string) $k
	 * @param string|null $v [optional]
	 */
	protected function si($k, $v = null) {
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
	 * @param string|array(string => string) $k
	 * @param string|null $v [optional]
	 */
	protected function siB($k, $v = null) {
		if ($this->isBackend()) {
			$this->si($k, $v);
		}
	}

	/**
	 * 2016-11-17
	 * @param string|array(string => string) $k
	 * @param string|null $v [optional]
	 */
	protected function siF($k, $v = null) {
		if ($this->isFrontend()) {
			$this->si($k, $v);
		}
	}

	/**
	 * 2016-11-17
	 * Этот метод инициализирирует информацию о ещё не прошедшем (случай allPay)
	 * или находящемся на модерации (случай Stripe и Omise) платеже.
	 * @see isWait()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	protected function siWait() {$this->si('State', __('Review'));}

	/**
	 * 2016-07-13
	 * @return string
	 */
	protected function testModeLabel() {return 'Test';}

	/**
	 * 2016-07-13
	 * @return string
	 */
	protected function testModeLabelLong() {return 'Test Mode';}

	/**
	 * 2017-01-13
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string
	 */
	final protected function titleB() {return $this->m()->titleB();}

	/**
	 * 2016-08-20
	 * @return T|null
	 */
	protected function transF() {return dfc($this, function() {return
		df_trans_by_payment_first($this->ii())
	;});}

	/**
	 * 2016-08-20
	 * @return T|null
	 */
	protected function transL() {return dfc($this, function() {return
		df_trans_by_payment_last($this->ii())
	;});}
}
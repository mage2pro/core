<?php
namespace Df\Payment;
use Df\Customer\Settings\BillingAddress as BA;
use Df\Payment\Exception as DFPE;
use Magento\Framework\Exception\CouldNotSaveException as CouldNotSave;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Api\Data\CartInterface as IQuote;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\Quote\Payment as QP;
// 2016-07-18
final class PlaceOrderInternal {
	/**
	 * 2017-03-12
	 * @param int|string $cartId
	 * @param bool $isGuest
	 */
	private function __construct($cartId, $isGuest) {$this->_cartId = $cartId; $this->_isGuest = $isGuest;}

	/**
	 * 2016-07-18
	 * @used-by p()
	 * @return string|null
	 * @throws CouldNotSave
	 */
	private function _place() {
		/** @var int $oid */
		try {
			BA::disable(!$this->s()->requireBillingAddress());
			try {$oid = df_quote_m()->placeOrder($this->qid());}
			finally {BA::restore();}
		}
		catch (\Exception $e) {throw new CouldNotSave(__($this->message($e)), $e);}
		return dfp_iia(df_order($oid), self::$DATA);
	}
	
	/**
	 * 2016-07-18
	 * 2016-10-24
	 * Сообщение для покупателя функция возвращает,
	 * а сообщение для администратора — логирует.
	 * @used-by _place()
	 * @param \Exception|DFPE $e
	 * @return string
	 */
	private function message(\Exception $e) {
		/** @var bool $isShipping */
		$isShipping = df_ets($e) === (string)__('Please specify a shipping method.');
		/** @var bool $isSpecific */
		if (!($isSpecific = $e instanceof DFPE)) {
			$e = df_ef($e);
		}
		if (!$isShipping) {
			df_log($e);
		}
		/** @var string $mc */
		/** @var string $md */
		list($mc, $md) =
			$isSpecific
			? [$e->messageC(), df_tag_if($e->messageD(), $e->isMessageHtml(), 'pre')]
			: [dfp_error_message(), df_etsd($e)]
		;
		return !$this->s()->test() ? $mc : df_cc_br($mc, __('Debug message:'), $md);
	}

	/**
	 * 2016-07-18
	 * @return int
	 */
	private function qid() {return dfc($this, function() {
		/** @var int|string $result */
		$result = $this->_cartId;
		/**
		 * 2016-07-18
		 * By analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Quote/Model/GuestCart/GuestCartManagement.php#L83-L87
		 */
		if ($this->_isGuest) {
			/** @var QuoteIdMask $quoteIdMask */
   			$quoteIdMask = df_load(QuoteIdMask::class, $result, true, 'masked_id');
			/** https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Quote/Setup/InstallSchema.php#L1549-L1557 */
			$result = $quoteIdMask['quote_id'];
			/** @var IQuote|Quote $quote */
			$quote = df_quote($result);
			$quote->setCheckoutMethod(IQM::METHOD_GUEST);
		}
		return $result;
	});}

	/**
	 * 2016-07-18
	 * @used-by _place()
	 * @used-by message()
	 * @return Settings
	 */
	private function s() {return dfc($this, function() {return dfpm(dfp(df_quote($this->qid())))->s();});}

	/**
	 * 2017-03-12
	 * Для анонимных покупателей $cartId — это строка вида «63b25f081bfb8e4594725d8a58b012f7».
	 * https://github.com/CKOTech/checkout-magento2-plugin/issues/10
	 * @var int|string
	 */
	private $_cartId;

	/**
	 * 2017-03-12
	 * @var bool
	 */
	private $_isGuest;

	/**
	 * 2016-07-18
	 * @used-by \Df\Payment\PlaceOrder::guest()
	 * @used-by \Df\Payment\PlaceOrder::registered()
	 * @param int|string $cartId
	 * @param bool $isGuest
	 * @return mixed|null
	 * @throws CouldNotSave
	 */
	static function p($cartId, $isGuest) {return (new self($cartId, $isGuest))->_place();}

	/**
	 * 2017-03-21
	 * 2016-07-01
	 * К сожалению, если передавать в качестве результата ассоциативный массив,
	 * то его ключи почему-то теряются. Поэтому запаковываем массив в JSON.
	 * @used-by \Df\GingerPaymentsBase\Method::getConfigPaymentAction()
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Df\PaypalClone\Method\Normal::getConfigPaymentAction()
	 * @used-by \Dfe\CheckoutCom\Method::ckoRedirectUrl()
	 * @param Method $m
	 * @param string $url
	 * @param array(string => mixed) $params [optional]
	 */
	static function setData(Method $m, $url, array $params = []) {
		$m->iiaSet(self::$DATA, df_json_encode(['params' => $params, 'url' => $url]))
	;}

	/**
	 * 2016-07-01
	 * @used-by _place()
	 * @used-by setData()
	 */
	private static $DATA = 'df_data';
}
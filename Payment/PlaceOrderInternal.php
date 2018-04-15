<?php
namespace Df\Payment;
use Df\Customer\Settings\BillingAddress as BA;
use Df\Payment\Exception as DFPE;
use Df\Payment\Method as M;
use Magento\Framework\Exception\CouldNotSaveException as CouldNotSave;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Api\Data\CartInterface as IQuote;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
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
	 * 2017-04-04
	 * Метод возвращает null для модулей, работающих без перенаправления:
	 * такие модули просто не инициализируют ключ @uses $REDIRECT_DATA
	 * @used-by \Df\Payment\PlaceOrderInternal::p()
	 * @return string|mixed[]
	 * @throws CouldNotSave|LE
	 */
	private function _place() {
		/** @var int $oid */
		try {
			BA::disable(!$this->m()->requireBillingAddress());
			try {$oid = df_quote_m()->placeOrder($this->qid());}
			finally {BA::restore();}
		}
		catch (\Exception $e) {throw new CouldNotSave(__($this->message($e)), $e);}
		/**
		 * 2018-04-14
		 * The previous code was:
		 * 		return dfp_iia(df_order($oid), self::$REDIRECT_DATA);
		 * I have changed it for the @see \Dfe\Tap\Model\Tap class,
		 * which is not a @see \Df\Payment\Method descendant and therefore not a singleton.
		 */
		$m = $this->m(); /** @var M $m */
		$m->orderPlaced($oid);
		$r = dfp_iia($m->getInfoInstance(), self::$REDIRECT_DATA);
		/**
		 * 2018-04-15
		 * https://www.upwork.com/messages/rooms/room_c037d73dc6d45dee9ad4a664a05ce541/story_89760d8131dae4d3d268f13009b89950
		 */
		return $m->skipDfwEncode() ? $r : dfw_encode($r);
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
	 * 2017-12-13
	 * @used-by _place()
	 * @used-by s()
	 * @return Method
	 */
	private function m() {return dfc($this, function() {return dfpm(dfp(df_quote($this->qid())));});}

	/**
	 * 2016-07-18
	 * @used-by _place()
	 * @used-by message()
	 * @return Settings
	 */
	private function s() {return dfc($this, function() {return $this->m()->s();});}

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
	 * @return string|mixed[]
	 * @throws CouldNotSave|LE
	 */
	static function p($cartId, $isGuest) {return (new self($cartId, $isGuest))->_place();}

	/**
	 * 2017-03-21
	 * @used-by \Df\Payment\Init\Action::action()
	 * @used-by \Dfe\CheckoutCom\Method::ckoRedirectUrl()
	 * @param M $m
	 * @param string $url
	 * @param array(string => mixed) $p [optional]
	 * @param bool $forceGet [optional]
	 */
	static function setRedirectData(M $m, $url, array $p = [], $forceGet = false) {$m->iiaSet(
		self::$REDIRECT_DATA, ['forceGet' => $forceGet, 'p' => $p, 'url' => $url]
	);}

	/**
	 * 2016-07-01
	 * @used-by _place()
	 * @used-by setRedirectData()
	 */
	private static $REDIRECT_DATA = 'df_redirect';
}
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
use \Exception as E;
# 2016-07-18
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
		catch (E $e) {throw new CouldNotSave(__($this->message($e)), $e);}
		/**
		 * 2018-04-14
		 * The previous code was:
		 * 		return dfp_iia(df_order($oid), self::$REDIRECT_DATA);
		 * I have changed it for the @see \Dfe\Tap\Model\Tap class,
		 * which is not a @see \Df\Payment\Method descendant and therefore not a singleton.
		 */
		$m = $this->m(); /** @var M $m */
		$m->orderPlaced($oid);
		$r = dfp_iia($m, self::$REDIRECT_DATA);
		# 2018-04-15
		# https://www.upwork.com/messages/rooms/room_c037d73dc6d45dee9ad4a664a05ce541/story_89760d8131dae4d3d268f13009b89950
		return $m->skipDfwEncode() ? $r : dfw_encode($r);
	}
	
	/**
	 * 2016-07-18
	 * 2016-10-24 Сообщение для покупателя функция возвращает, а сообщение для администратора — логирует.
	 * @used-by self::_place()
	 * @param E|DFPE $e
	 */
	private function message(E $e):string {
		$isShipping = df_xts($e) === (string)__('Please specify a shipping method.'); /** @var bool $isShipping */
		/** @var bool $isSpecific */
		if (!($isSpecific = $e instanceof DFPE)) {
			$e = df_xf($e);
		}
		if (!$isShipping) {
			df_log($e);
		}
		# 2020-03-02, 2022-10-31
		# 1) Symmetric array destructuring requires PHP ≥ 7.1:
		#		[$a, $b] = [1, 2];
		# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
		# We should support PHP 7.0.
		# https://3v4l.org/3O92j
		# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
		# https://stackoverflow.com/a/28233499
		list($mc, $md) =
			$isSpecific
			? [$e->messageC(), df_tag_if($e->messageD(), $e->isMessageHtml(), 'pre')]
			: [dfp_error_message(), df_xtsd($e)]
		; /** @var string $mc */ /** @var string $md */
		return !$this->s()->test() ? $mc : df_cc_br($mc, __('Debug message:'), $md);
	}

	/**
	 * 2016-07-18
	 * @used-by self::_place()
	 * @used-by self::m()
	 */
	private function qid():int {return dfc($this, function() {
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
	 * @used-by self::_place()
	 * @used-by self::s()
	 */
	private function m():Method {return dfc($this, function() {return dfpm(dfp(df_quote($this->qid())));});}

	/**
	 * 2016-07-18
	 * @used-by self::_place()
	 * @used-by self::message()
	 */
	private function s():Settings {return dfc($this, function() {return $this->m()->s();});}

	/**
	 * 2017-03-12
	 * Для анонимных покупателей $cartId — это строка вида «63b25f081bfb8e4594725d8a58b012f7».
	 * https://github.com/CKOTech/checkout-magento2-plugin/issues/10
	 * @var int|string
	 */
	private $_cartId;

	/**
	 * 2017-03-12
	 * @used-by self::__construct()
	 * @used-by self::qid()
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
	 * @used-by \Dfe\CheckoutCom\Method::need3DS()
	 * @param M $m
	 * @param string $url
	 * @param array(string => mixed) $p [optional]
	 * @param bool $forceGet [optional]
	 */
	static function setRedirectData(M $m, $url, array $p = [], $forceGet = false):array {$m->iiaSet(
		self::$REDIRECT_DATA, ['forceGet' => $forceGet, 'p' => $p, 'url' => $url]
	);}

	/**
	 * 2016-07-01
	 * @used-by self::_place()
	 * @used-by self::setRedirectData()
	 */
	private static $REDIRECT_DATA = 'df_redirect';
}
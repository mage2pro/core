<?php
namespace Df\Payment;
use Df\Customer\Settings\BillingAddress;
use Df\Payment\Exception as DFPE;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\CartInterface as IQuote;
use Magento\Quote\Api\CartManagementInterface as IQM;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteManagement as QM;
use Magento\Quote\Model\Quote\Payment as QP;
// 2016-07-18
class PlaceOrderInternal extends \Df\Core\O {
	/**
	 * 2016-07-18
	 * @return mixed|null
	 * @throws CouldNotSaveException
	 */
	private function _place() {
		/** @var IQM|QM $qm */
		$qm = df_o(IQM::class);
		/** @var mixed $result */
		try {
			BillingAddress::disable(!$this->ss()->askForBillingAddress());
			try {
				/** @var int $orderId */
				$orderId = $qm->placeOrder($this->quoteId());
			}
			finally {
				BillingAddress::restore();
			}
			$result = df_order($orderId)->getPayment()->getAdditionalInformation(PlaceOrder::DATA);
		}
		catch (\Exception $e) {
			throw new CouldNotSaveException(__($this->message($e)), $e);
		}
		return $result;
	}

	/** @return bool */
	private function guest() {return $this[self::$P__GUEST];}

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
		/** @var bool $isSpecific */
		$isSpecific = $e instanceof DFPE;
		if (!$isSpecific) {
			$e = df_ef($e);
		}
		df_log($e);
		/** @var string $mc */
		/** @var string $md */
		list($mc, $md) =
			$isSpecific
			? [$e->messageC(), df_tag_if($e->messageD(), $e->isMessageHtml(), 'pre')]
			: [dfp_error_message(), df_etsd($e)]
		;
		return !$this->ss()->test() ? $mc : df_cc_br($mc, __('Debug message:'), $md);
	}

	/**
	 * 2016-07-18
	 * @return QP
	 */
	private function payment() {return dfc($this, function() {return $this->quote()->getPayment();});}

	/**
	 * 2016-07-18
	 * @return Method
	 */
	private function paymentMethod() {return dfc($this, function() {return
		df_ar($this->payment()->getMethodInstance(), Method::class)
	;});}

	/**
	 * 2016-07-18
	 * @return IQuote|Quote
	 */
	private function quote() {return dfc($this, function() {return df_quote($this->quoteId());});}

	/**
	 * 2016-07-18
	 * @return int
	 */
	private function quoteId() {return dfc($this, function() {
		/** @var int|string $result */
		$result = $this[self::$P__QUOTE_ID];
		/**
		 * 2016-07-18
		 * By analogy with https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Quote/Model/GuestCart/GuestCartManagement.php#L83-L87
		 */
		if ($this->guest()) {
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
	 * @return Settings
	 */
	private function ss() {return dfc($this, function() {return $this->paymentMethod()->s();});}

	/**
	 * 2016-07-18
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__GUEST, DF_V_BOOL)
			/**
			 * 2016-07-19
			 * Раньше тут ошибочно стояла проверка @see DF_V_NAT.
			 * Она была ошибочна, потому что для анонимных покупателей
			 * идентификатором корзины является строка вида «63b25f081bfb8e4594725d8a58b012f7».
			 * https://github.com/CKOTech/checkout-magento2-plugin/issues/10
			 */
			->_prop(self::$P__QUOTE_ID, DF_V_STRING_NE)
		;
	}

	/** @var string */
	private static $P__GUEST = 'guest';
	/** @var string */
	private static $P__QUOTE_ID = 'quote_id';

	/**
	 * 2016-07-18
	 * @param int|string $cartId
	 * Для анонимных покупателей $cartId — это строка вида «63b25f081bfb8e4594725d8a58b012f7»
	 * @param bool $isGuest
	 * @return mixed|null
	 * @throws CouldNotSaveException
	 */
	public static function p($cartId, $isGuest) {return (new self([
		self::$P__GUEST => $isGuest, self::$P__QUOTE_ID => $cartId
	]))->_place();}
}
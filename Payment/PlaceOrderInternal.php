<?php
namespace Df\Payment;
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
			/** @var int $orderId */
			$orderId = $qm->placeOrder($this->quoteId());
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
	 * @param \Exception $e
	 * @return string
	 */
	private function message(\Exception $e) {
		/** @var string $result */
		if ($e instanceof Exception) {
			$result = $e->getMessageForCustomer();
			df_log($e->getMessageForDeveloper());
			df_log($e);
		}
		else {
			/** @var \Exception $ef */
			$ef = df_ef($e);
			df_log($ef);
			/** @var array(string|Phrase) $messageA */
			$messageA[]= __('Sorry, the payment attempt is failed.');
			$messageA[]= __('Please try again, or try another payment method.');
			if ($this->ss()->test()) {
				$messageA[]= __('Debug message:');
				$messageA[]= df_ets($ef);
			}
			$result = implode('<br/>', $messageA);
		}
		return $result;
	}

	/**
	 * 2016-07-18
	 * @return QP
	 */
	private function payment() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->quote()->getPayment();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @return Method
	 */
	private function paymentMethod() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->payment()->getMethodInstance();
			df_assert_is(Method::class, $this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @return IQuote|Quote
	 */
	private function quote() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_quote($this->quoteId());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @return int
	 */
	private function quoteId() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
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
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @return Settings
	 */
	private function ss() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->paymentMethod()->ss();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-18
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$P__GUEST, RM_V_BOOL)
			->_prop(self::$P__QUOTE_ID, RM_V_NAT)
		;
	}

	/** @var string */
	private static $P__GUEST = 'guest';
	/** @var string */
	private static $P__QUOTE_ID = 'quote_id';

	/**
	 * 2016-07-18
	 * @param int $cartId
	 * @param bool $isGuest
	 * @return mixed|null
	 * @throws CouldNotSaveException
	 */
	public static function p($cartId, $isGuest) {
		return (new self([self::$P__GUEST => $isGuest, self::$P__QUOTE_ID => $cartId]))->_place();
	}
}
<?php
namespace Df\StripeClone;
use Df\Sales\Model\Order\Payment as DfOP;
use Df\StripeClone\Facade\ICard as C;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-02-11
/** @see \Dfe\Moip\CardFormatter */
class CardFormatter {
	/**    
	 * 2017-02-12
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return C
	 */	
	function c() {return $this->_c;}

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string|null
	 */
	final function country() {/** @var string|null $c */return
		!($c = $this->_c->country()) ? $c : df_country_ctn(strtoupper($c))
	;}

	/**
	 * 2017-02-11
	 * 2017-07-19 Some PSPs like Moip does not return the card's expiration date.
	 * @see \Dfe\Moip\Facade\Card::expMonth()
	 * @see \Dfe\Moip\Facade\Card::expYear()
	 * https://github.com/mage2pro/moip/blob/0.7.6/Facade/Card.php#L84-L104
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @return string|null
	 */
	final function exp() {/** @var int $e */ /** @var int $m */return
		!($m = $this->_c->expMonth()) || !($e = $this->_c->expYear()) ? null :
			implode(' / ', [sprintf('%02d', $m), $e])
	;}

	/**
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @return array(string => string)
	 */
	final function ii() {return [
		DfOP::COUNTRY => $this->country()
		,OP::CC_EXP_MONTH => $this->_c->expMonth()
		,OP::CC_EXP_YEAR => $this->_c->expYear()
		,OP::CC_LAST_4 => $this->_c->last4()
		,OP::CC_OWNER => $this->_c->owner()
		,OP::CC_TYPE => $this->_c->brand()
	];}

	/**          
	 * 2017-02-11
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @see \Dfe\Moip\CardFormatter::label()
	 * @return string
	 */
	function label() {return dfp_card_format_last4($this->_c->last4(), $this->_c->brand());}

	/**
	 * 2017-02-11
	 * 2017-07-19
	 * It looks like we a forced to make the constructor protected, not private,
	 * despite it is used only inside the class.
	 * @see \Df\Payment\Facade::__construct()
	 * https://github.com/mage2pro/core/blob/2.8.25/Payment/Facade.php#L18-L27
	 * @used-by s()
	 * @param C $c
	 */
	final protected function __construct(C $c) {$this->_c = $c;}

	/**
	 * 2017-02-11
	 * @var C
	 */
	private $_c;
	
	/**
	 * 2017-07-19
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Method::chargeNew()
	 * @param string|object $m
	 * @param C $c
	 * @return self
	 */
	final static function s($m, C $c) {return dfcf(function($m, C $c) {
		/**
		 * 2017-07-19
		 * Unable to reduce the implementation to:
		 * 		df_new(df_con_hier($m, self::class), $c);
		 * because @uses __construct() is protected.
		 * It is similar to @see \Df\Payment\Facade::s()
		 * https://github.com/mage2pro/core/blob/2.8.25/Payment/Facade.php#L75-L87
		 */
		$class = df_con_hier($m, self::class); /** @var string $class */
		return new $class($c);
	}, func_get_args());}
}
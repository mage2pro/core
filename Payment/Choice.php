<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Df\Payment\W\Event;
use Magento\Framework\Phrase;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-04-17
 * @see \Df\GingerPaymentsBase\Choice
 * @see \Dfe\AllPay\Choice
 * @see \Dfe\AlphaCommerceHub\Choice
 * @see \Dfe\IPay88\Choice
 * @see \Dfe\Moip\Choice
 * @see \Dfe\PostFinance\Choice
 * @see \Dfe\Robokassa\Choice
 */
class Choice {
	/**
	 * 2017-04-17
	 * @used-by \Df\Payment\Block\Info::choiceT()
	 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * @see \Df\GingerPaymentsBase\Choice::title()
	 * @see \Dfe\AllPay\Choice::title()
	 * @see \Dfe\IPay88\Choice::title()
	 * @see \Dfe\Moip\Choice::title()
	 * @see \Dfe\PostFinance\Choice::title()
	 * @see \Dfe\Robokassa\Choice::title()
	 * @return Phrase|string|null
	 */
	function title() {return null;}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by tm()
	 * @used-by \Df\GingerPaymentsBase\Choice::optionCodeI()
	 * @used-by \Dfe\AllPay\Choice::title()
	 * @used-by \Dfe\AlphaCommerceHub\Choice::title()
	 * @used-by \Dfe\Moip\Choice::title()
	 * @used-by \Dfe\Robokassa\Choice::title()
	 * @return M
	 */
	protected function m() {return $this->_m;}

	/**
	 * 2017-04-17
	 * @used-by \Df\GingerPaymentsBase\Choice::option()
	 * @used-by \Dfe\AlphaCommerceHub\Choice::id()
	 * Возвращает параметры первичного запроса магазина к ПС.
	 * @param string|string[]|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final protected function req($k = null) {return $this->tm()->req($k);}

	/**
	 * 2017-04-17 Возвращает параметры ответа на первичный запрос магазина к ПС.
	 * 2017-11-20 @todo Currently, it is never used.
	 * @param string|string[]|null $k [optional]
	 * @return array(string => string)|string|null
	 */
	final protected function res0($k = null) {return $this->tm()->res0($k);}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Choice::title()
	 * @used-by \Dfe\IPay88\Choice::title()
	 * @used-by \Dfe\PostFinance\Choice::title()
	 * @used-by \Dfe\Robokassa\Choice::title()
	 * @param string[] ...$k
	 * @return Event|string|null
	 */
	protected function responseF(...$k) {return $this->tm()->responseF(...$k);}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\GingerPaymentsBase\Choice::title()
	 * @param string|null $k [optional]
	 * @return \Df\Payment\Settings
	 */
	protected function s($k = null) {return $this->_m->s($k);}

	/**
	 * 2017-04-17
	 * @used-by s()
	 * @param M $m
	 */
	private function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-04-17
	 * @used-by req()
	 * @used-by responseF()
	 * @return \Df\Payment\TM
	 */
	private function tm() {return df_tm($this->m());}

	/**
	 * 2017-04-17
	 * @used-by __construct()
	 * @used-by s()
	 * @used-by tm()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-04-17
	 * @used-by dfp_choice()
	 * @param II|OP|QP|O|Q|T $op
	 * @return self
	 */
	final static function f($op) {return dfcf(function(OP $op) {
		$c = df_con_hier($m = df_ar(dfpm($op), M::class), __CLASS__); /** @var string $c */ /** @var M $m */
		return new $c($m);
	}, [dfp($op)]);}
}
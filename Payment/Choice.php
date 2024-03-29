<?php
namespace Df\Payment;
use Df\Payment\Method as M;
use Df\Payment\W\Event;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-04-17
 * @see \Dfe\GingerPaymentsBase\Choice
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
	 * @see \Dfe\GingerPaymentsBase\Choice::title()
	 * @see \Dfe\AllPay\Choice::title()
	 * @see \Dfe\IPay88\Choice::title()
	 * @see \Dfe\Moip\Choice::title()
	 * @see \Dfe\PostFinance\Choice::title()
	 * @see \Dfe\Robokassa\Choice::title()
	 */
	function title():string {return '';}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by self::tm()
	 * @used-by \Dfe\GingerPaymentsBase\Choice::optionCodeI()
	 * @used-by \Dfe\AllPay\Choice::title()
	 * @used-by \Dfe\AlphaCommerceHub\Choice::title()
	 * @used-by \Dfe\Moip\Choice::title()
	 * @used-by \Dfe\Robokassa\Choice::title()
	 */
	protected function m():M {return $this->_m;}

	/**
	 * 2017-04-17 Возвращает параметры первичного запроса магазина к ПС.
	 * @used-by \Dfe\GingerPaymentsBase\Choice::option()
	 * @used-by \Dfe\AlphaCommerceHub\Choice::id()
	 * @return array(string => string)|string|null
	 */
	final protected function req(string ...$k) {return $this->tm()->req(...$k);}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\AllPay\Choice::title()
	 * @used-by \Dfe\IPay88\Choice::title()
	 * @used-by \Dfe\PostFinance\Choice::title()
	 * @used-by \Dfe\Robokassa\Choice::title()
	 * @return Event|string|null
	 */
	protected function responseF(string ...$k) {return $this->tm()->responseF(...$k);}

	/**
	 * 2017-04-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Dfe\GingerPaymentsBase\Choice::title()
	 */
	protected function s(string $k = ''):Settings {return $this->_m->s($k);}

	/**
	 * 2017-04-17
	 * @used-by self::s()
	 */
	private function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-04-17
	 * @used-by self::req()
	 * @used-by self::responseF()
	 */
	private function tm():TM {return df_tm($this->m());}

	/**
	 * 2017-04-17
	 * @used-by self::__construct()
	 * @used-by self::s()
	 * @used-by self::tm()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-04-17
	 * @used-by dfp_choice()
	 * @param II|OP|QP|O|Q|T $op
	 */
	final static function f($op):self {return dfcf(function(OP $op) {
		$c = df_con_hier($m = df_ar(dfpm($op), M::class), __CLASS__); /** @var string $c */ /** @var M $m */
		return new $c($m);
	}, [dfp($op)]);}
}
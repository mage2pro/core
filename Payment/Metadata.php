<?php
namespace Df\Payment;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;
/** @method static Metadata s() */
final class Metadata extends \Df\Config\Source {
	/**
	 * 2016-07-05
	 * @override
	 * @see \Df\Config\Source::keys()
	 * @return string[]
	 */
	function keys() {return ['customer.name', 'order.id', 'order.items', 'store.domain', 'store.name', 'store.url'];}

	/**
	 * 2016-03-09
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @used-by \Df\Payment\Charge::metadata()
	 * @used-by \Dfe\CheckoutCom\Method::charge()
	 * @return array(string => string)
	 */
	function map():array {return array_combine($this->keys(), [
		'Customer Name', 'Order ID', 'Order Items', 'Store Domain', 'Store Name', 'Store URL'
	]);}

	/**
	 * 2016-03-14
	 * 2017-03-06 Ключами результата являются системные имена переменных.
	 * @used-by \Df\Payment\Charge::vars()
	 * @param Store $s
	 * @param O|Q $oq
	 * @return array(string => string)
	 */
	static function vars(Store $s, $oq) {return array_combine(self::s()->keys(), [
		df_oq_customer_name($oq)
		,df_oq_iid($oq)
		,df_oqi_s($oq)
		,df_domain_current($s)
		,$s->getFrontendName()
		,df_store_url_link($s)
	]);}
}
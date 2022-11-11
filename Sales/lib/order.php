<?php
use Df\Sales\Model\Order as DFO;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as IO;
use Magento\Sales\Api\OrderRepositoryInterface as IOrderRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\OrderRepository;

/**
 * 2016-05-04
 * How to get an order by its id programmatically? https://mage2.pro/t/1518    
 * @see df_quote()
 * @used-by df_oi_get()
 * @used-by df_oi_save()
 * @used-by dfp_refund()
 * @used-by \Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \CanadaSatellite\Core\Plugin\MageWorx\OrderEditor\Block\Adminhtml\Sales\Order\Edit\Wrapper::aroundGetTemplate() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/237)
 * @used-by \Df\Payment\Method::o()
 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * @used-by \Df\Payment\Operation::o()
 * @used-by \Df\Payment\Operation\Source\Order::oq()
 * @used-by \Df\Payment\PlaceOrderInternal::_place()
 * @used-by \Df\Payment\TM::confirmed()
 * @used-by \Df\Payment\W\Handler::o()
 * @used-by \Df\Sales\Model\Order\Payment::processActionS()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::o()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::o()
 * @used-by \Inkifi\Mediaclip\Event::o()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t02()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Get::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t01()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t02()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t03()
 * @param int|O|OP|null $o [optional]
 * @throws InputException|LE|NoSuchEntityException
 */
function df_order($o = null):O {return df_is_o($o) ? $o : (
	$o instanceof OP ? df_order_by_payment($o) : (
		is_numeric($o = $o ?: df_request('order_id')) ? df_order_r()->get($o)
			: df_error('df_order: invalid argument: %s.', df_type($o))
	)
);}

/**
 * 2016-05-21
 * How to get an order backend URL programmatically? https://mage2.pro/t/1639
 * 2016-05-22
 * Даже если включена опция «Add Secret Key to URLs», адреса без ключей всё равно работают.
 * https://mage2.pro/tags/backend-url-secret-key
 * "How to skip adding the secret key to a backend URL using the «_nosecret» parameter?" https://mage2.pro/t/1644
 * 2016-08-24
 * @see df_customer_backend_url()
 * @see df_cm_backend_url()
 * @param O|int $o
 */
function df_order_backend_url($o):string {return df_url_backend_ns('sales/order/view', ['order_id' => df_idn($o)]);}

/**
 * 2016-05-07
 * @used-by df_order()
 * @param OP $p
 * @return O|DFO
 * @throws LE
 */
function df_order_by_payment(OP $p) {return dfcf(function(OP $p) {
	$result = $p->getOrder(); /** @var O|DFO $result */
	# 2016-05-08
	# Раньше здесь стояла проверка !$result->getId()
	# Это оказалось не совсем правильным, потому что в оплаты размещаемого в данный момент заказа
	# у этого заказа ещё нет идентификатора (потому что он не сохранён),
	# но вот increment_id для него создаётся заранее
	# (в том числе, чтобы другие объекты, да и платёжные модули могли к нему привязываться).
	if (!$result->getIncrementId()) {
		throw new LE(__('The order no longer exists.'));
	}
	/**
	 * 2016-03-26
	 * Очень важно! Иначе order создаст свой экземпляр payment:
	 * @used-by \Magento\Sales\Model\Order::getPayment()
	 */
	$result[IO::PAYMENT] = $p;
	return $result;
}, [$p]);}

/**
 * 2017-03-18
 * @used-by df_order_ds()
 */
function df_order_config():Config {return df_o(Config::class);}

/**
 * 2016-05-04
 * @used-by df_order()
 * @return IOrderRepository|OrderRepository
 */
function df_order_r() {return df_o(IOrderRepository::class);}

/**
 * 2016-03-14
 * 2016-07-02
 * Метод @uses \Magento\Sales\Model\Order::getShippingMethod()
 * некорректно работает с параметром $asObject = true при отсутствии у заказа способа доставки
 * (такое может быть, в частности, когда заказ содержит только виртуальные товары):
 * list($carrierCode, $method) = explode('_', $shippingMethod, 2);
 * Здесь $shippingMethod равно null, что приводит к сбою
 * Notice: Undefined offset: 1 in app/code/Magento/Sales/Model/Order.php on line 1203
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L1191-L1206
 * Поэтому сначала смотрим, имеется ли у заказа способ доставки,
 * вызывая @uses \Magento\Sales\Model\Order::getShippingMethod() с параметром $asObject = false
 * @used-by \Dfe\Stripe\P\Address::p()
 * @param O $o
 */
function df_order_shipping_title(O $o):string {return /** @var string $c */
	!$o->getShippingMethod() || !($c = $o->getShippingMethod(true)['method']) ? '' :
		df_cfg("carriers/$c/title")
;}

/**
 * 2017-03-18
 * @used-by \Df\Payment\Observer\VoidT::execute()
 * @used-by \Df\Sales\Model\Order\Payment::processActionS()
 * @used-by \Df\Sales\Plugin\Model\ResourceModel\Order\Handler\State::aroundCheck()
 * @param string $state
 */
function df_order_ds($state):string {return df_order_config()->getStateDefaultStatus($state);}
<?php
use Df\Sales\Model\Order as DfOrder;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface as IHistory;
use Magento\Sales\Api\OrderRepositoryInterface as IOrderRepository;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Status\History;
use Magento\Sales\Model\OrderRepository;

/**
 * 2016-05-04
 * How to get an order by its id programmatically? https://mage2.pro/t/1518
 * @param int $id
 * @return OrderInterface|Order
 */
function df_order($id) {return df_order_r()->get($id);}

/**
 * 2016-05-07
 * @param OP $payment
 * @return Order|DfOrder
 * @throws LE
 */
function df_order_by_payment(OP $payment) {
	/** @var Order|DfOrder $result */
	$result = $payment->getOrder();
	/**
	 * 2016-05-08
	 * Раньше здесь стояла проверка !$result->getId()
	 * Это оказалось не совсем правильным, потому что в оплаты размещаемого в данный момент заказа
	 * у этого заказа ещё нет идентификатора (потому что он не сохранён),
	 * но вот increment_id для него создаётся заранее
	 * (в том числе, чтобы другие объекты, да и платёжные модули могли к нему привязываться).
	 */
	if (!$result->getIncrementId()) {
		throw new LE(__('The order no longer exists.'));
	}
	/**
	 * 2016-03-26
	 * Очень важно! Иначе order создаст свой экземпляр payment:
	 * @used-by \Magento\Sales\Model\Order::getPayment()
	 */
	$result[OrderInterface::PAYMENT] = $payment;
	return $result;
}

/**
 * 2016-03-09
 * @param Order $order
 * @return string
 */
function df_order_customer_name(Order $order) {
	/** @var string[ $result */
	$result = df_cc_s(
		$order->getCustomerFirstname()
		, $order->getCustomerMiddlename()
		, $order->getCustomerLastname()
	);
	if (!$result) {
		/** @var \Magento\Customer\Model\Customer $customer */
		$customer = $order->getCustomer();
		if ($customer) {
			$result = $customer->getName();
		}
	}
	if (!$result) {
		/** @var \Magento\Sales\Model\Order\Address|null $ba */
		$ba = $order->getBillingAddress();
		if ($ba) {
			$result = $ba->getName();
		}
	}
	if (!$result) {
		/** @var \Magento\Sales\Model\Order\Address|null $ba */
		$sa = $order->getShippingAddress();
		if ($sa) {
			$result = $sa->getName();
		}
	}
	return $result;
}

/**
 * 2016-05-03
 * Заметил, что у order item, которым соответствуют простые варианты настраиваемого товара,
 * цена почему-то равна нулю и содержится в родительском order item.
 * @param OrderItem|OrderItemInterface $item
 * @return float
 */
function df_order_item_price(OrderItemInterface $item) {
	return $item->getPrice() ?: (
		$item->getParentItem() ? df_order_item_price($item->getParentItem()) : 0
	);
}

/**
 * 2016-03-09
 * @param Order $order
 * 2016-07-04
 * Добавил этот параметр для модуля AllPay, где разделителем должен быть символ #.
 * @param string $separator [optional]
 * @return string
 */
function df_order_items(Order $order, $separator = ', ') {
	return df_ccc($separator, df_map(function(OrderItem $item) {
		/** @var int $qty */
		$qty = $item->getQtyOrdered();
		/**
		 * 2016-03-24
		 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
		 * будет содержать как настраиваемый товар, так и его простой вариант.
		 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
		 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
		 */
		return $item->getParentItem() ? null :
			df_cc_s($item->getName(), 1 >= $qty ? null : "({$qty})")
		;
	}, $order->getItems()));
}

/**
 * 2016-05-04
 * @return IOrderRepository|OrderRepository
 */
function df_order_r() {return df_o(IOrderRepository::class);}

/**
 * 2016-05-06
 * https://mage2.pro/t/1543
 * @see df_invoice_send_email()
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_order_send_email() instead of @see df_invoice_send_email()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @param Order $order
 * @return void
 */
function df_order_send_email(Order $order) {
	/** @var OrderSender $sender */
	$sender = df_o(OrderSender::class);
	$sender->send($order);
	/** @var History|IHistory $history */
	$history = $order->addStatusHistoryComment(__(
		'You have confirmed the order to the customer via email.'
	));
	$history->setIsCustomerNotified(true);
	$history->save();
}

/**
 * 2016-03-14
 * @param Order $order
 * @return string
 */
function df_order_shipping_title(Order $order) {
	/**
	 * 2016-07-02
	 * Метод @uses \Magento\Sales\Model\Order::getShippingMethod()
	 * некорректно работает с параметром $asObject = true при отсутствии у заказа способа доставки
	 * (такое может быть, в частности, когда заказ содержит только виртуальные товары):
	 * list($carrierCode, $method) = explode('_', $shippingMethod, 2);
	 * Здесь $shippingMethod равно null, что приводит к сбою
	 * Notice: Undefined offset: 1 in app/code/Magento/Sales/Model/Order.php on line 1203
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Model/Order.php#L1191-L1206
	 * Поэтому сначала смотрим, имеется ли у заказа способ доставки,
	 * вызывая @uses \Magento\Sales\Model\Order::getShippingMethod() с параметром $asObject = false:
	 */
	/** @var string $result */
	$result = '';
	if ($order->getShippingMethod()) {
		/** @var string $code */
		$code = $order->getShippingMethod($asObject = true)['method'];
		if ($code) {
			$result = df_cfg(df_cc_path('carriers', $code, 'title'));
		}
	}
	return $result;
}

/**
 * 2016-05-21
 * How to get an order backend URL programmatically? https://mage2.pro/t/1639
 * 2016-05-22
 * Даже если включена опция «Add Secret Key to URLs», адреса без ключей всё равно работают.
 * https://mage2.pro/tags/backend-url-secret-key
 * How to skip adding the secret key to a backend URL using the «_nosecret» parameter?
 * https://mage2.pro/t/1644
 * @param int $id
 * @return string
 */
function df_order_backend_url($id) {
	df_assert($id);
	return df_url_backend('sales/order/view', ['order_id' => $id, '_nosecret' => true]);
}


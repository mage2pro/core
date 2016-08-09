<?php
use Df\Sales\Model\Order as DfOrder;
use Df\Sales\Model\Order\Payment as DfPayment;
use Dfe\SalesSequence\Model\Meta;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface as IHistory;
use Magento\Sales\Api\OrderRepositoryInterface as IOrderRepository;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Status\History;
use Magento\Sales\Model\OrderRepository;
use Magento\SalesSequence\Model\Meta as _Meta;
use Magento\Store\Api\Data\StoreInterface;
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
function df_credit_memo_backend_url($id) {
	df_assert($id);
	return df_url_backend('sales/order_creditmemo/view', [
		'creditmemo_id' => $id, '_nosecret' => true
	]);
}

/**
 * 2016-03-27
 * @param OrderInterface|Order $order
 * @param int $transactionId
 * @return Invoice|null
 */
function df_invoice_by_transaction(OrderInterface $order, $transactionId) {
	return DfPayment::getInvoiceForTransactionId($order, $transactionId);
}

/**
 * 2016-07-15
 * Usually, when you have received a payment confirmation from a payment system,
 * you should use @see df_order_send_email() instead of @see df_invoice_send_email()
 * What is the difference between InvoiceSender and OrderSender? https://mage2.pro/t/1872
 * @param Invoice $invoice
 * @return void
 */
function df_invoice_send_email(Invoice $invoice) {
	/** @var InvoiceSender $sender */
	$sender = df_o(InvoiceSender::class);
	$sender->send($invoice);
}

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
	$result = df_ccc(' ',
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
	return df_ccc(', ', df_map(function(OrderItem $item) {
		/** @var int $qty */
		$qty = $item->getQtyOrdered();
		/**
		 * 2016-03-24
		 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
		 * будет содержать как настраиваемый товар, так и его простой вариант.
		 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
		 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
		 */
		return $item->getParentItem() ? null : df_ccc(' ',
			$item->getName(), 1 >= $qty ? null : "({$qty})"
		);
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
			$result = df_cfg(df_cc_xpath('carriers', $code, 'title'));
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

/**
 * 2016-01-11
 * @return array(string => string)
 */
function df_sales_entity_types() {
	return [
		'Order' => 'order'
		,'Invoice' => 'invoice'
		,'Shipment' => 'shipment'
		,'Credit Memo' => 'creditmemo'
	];
}

/**
 * 2016-01-29
 * @param string $type
 * @return int|false
 */
function df_sales_entity_type_index($type) {
	return array_search($type, array_values(df_sales_entity_types()));
}

/**
 * 2016-01-11
 * @return \Magento\SalesSequence\Model\Manager
 */
function df_sales_seq_m() {return df_o(\Magento\SalesSequence\Model\Manager::class);}

/**
 * 2016-01-26
 * @param string $entityType
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return _Meta|Meta
 */
function df_sales_seq_meta($entityType, $store = null) {
	/** @var array(int => array(string => _Meta)) */
	static $cache;
	$store = df_store_id($store);
	/** @var string $cacheKey */
	$cacheKey = implode([$store, $entityType]);
	if (!isset($cache[$cacheKey])) {
		/** @var \Magento\SalesSequence\Model\ResourceModel\Meta $resource */
		$resource = df_o(\Magento\SalesSequence\Model\ResourceModel\Meta::class);
		/**
		 * 2016-01-26
		 * По аналогии с @see \Magento\SalesSequence\Model\Manager::getSequence()
		 * https://github.com/magento/magento2/blob/d50ee5/app/code/Magento/SalesSequence/Model/Manager.php#L48
		 */
		$cache[$cacheKey] = $resource->loadByEntityTypeAndStore($entityType, $store);
	}
	return $cache[$cacheKey];
}

/**
 * 2016-01-11
 * Первая реализация была наивной:
 * return df_sales_seq_m()->getSequence($entityTypeCode, df_store_id($store))->getNextValue();
 * Она неправильна тем, что метод
 * @see \Magento\SalesSequence\Model\Sequence::getNextValue()
 * @see \Magento\Framework\DB\Sequence\SequenceInterface::getNextValue()
 * не только возвращает результат, но и обладает сторонним эффектом,
 * добавляя в таблицу новую строку:
 * $this->connection->insert($this->meta->getSequenceTable(), []);
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L82
 * Наша функция не имеет сторонних эффектов.
 *
 * @param string $entityTypeCode
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string
 */
function df_sales_seq_next($entityTypeCode, $store = null) {
	/**
	 * 2016-01-11
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L83
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Setup/InstallSchema.php#L123-L129
	 */
	return df_next_increment(df_sales_seq_meta($entityTypeCode, $store)->getSequenceTable());
}



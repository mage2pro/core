<?php
use Df\Payment\Method as M;
use Df\Sales\Model\Order\Payment as DfOP;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Payment\Model\MethodInterface as IM;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-03-21
 * @used-by df_trans_is_test()
 * @used-by dfp_iia()
 * @used-by dfpm()
 * @used-by \Df\Payment\Method::getInfoInstance()
 * @used-by \Df\Payment\Operation\Source\Quote::ii()
 * @used-by \Df\Payment\PlaceOrderInternal::s()
 * @used-by \Df\Payment\Choice::f()
 * @used-by \Df\Payment\W\Nav::op()
 * @used-by \Df\Sales\Block\Order\Total::op()
 * @used-by \Dfe\AllPay\Total\Quote::collect()
 * @param II|OP|QP|O|Q|T $v
 * @return II|OP|QP|null
 */
function dfp($v) {return $v instanceof II ? $v : (
	$v instanceof T ? dfp_get($v->getPaymentId()) : (df_is_oq($v) ? $v->getPayment() : df_error())
);}

/**               
 * 2017-03-26
 * Вызов этой функции приводит к добавлению транзакции типа $action:
 * https://github.com/mage2pro/core/blob/2.4.2/Payment/W/Nav.php#L100-L114
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::action() 
 * @param OP $p
 * @param string $action
 */
function dfp_action(OP $p, $action) {DfOP::action($p, $action);}

/**
 * 2016-05-20
 * @see df_ci_add()
 * @used-by \Df\Payment\Method::iiaAdd()  
 * @used-by \Df\Payment\Observer\Multishipping::execute()
 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
 * @param II|I|OP|QP $p
 * @param array $info
 */
function dfp_add_info(II $p, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$p->setAdditionalInformation($key, $value);
	}
}

/**
 * 2017-01-19
 * 2017-02-09
 * Контейнеры используются для хранения в едином поле множества значений.
 * Пока это возможность используется только в сценарии возврата:
 * если возврат был инициирован на стороне Magento, то мы запоминаем посредством dfp_container_add()
 * его идентификатор, чтобы когда платёжная система сообщит нам о нашем же возврате через webhook,
 * мы знали, что этот возврат мы уже обрабатывали и не обрабатывали бы его повторно:
 * @used-by \Df\StripeClone\Method::_refund()
 * https://github.com/mage2pro/core/blob/1.12.16/StripeClone/Method.php?ts=4#L262-L273
 * @param II|I|OP|QP $p
 * @param string $k
 * @param string $v
 */
function dfp_container_add(II $p, $k, $v) {$p->setAdditionalInformation($k, df_json_encode(
	array_merge(dfp_container_get($p, $k), [$v])
));}

/**
 * 2017-01-19
 * 2017-02-09
 * Пока эта функция имеет лишь вспомогательное значение:
 * @used-by dfp_container_add()
 * @used-by dfp_container_has()
 * @param II|I|OP|QP $p
 * @param string $k
 * @return string[]
 * 2017-03-11
 * Формально возвращает array(string => mixed), но реально — string[].
 */
function dfp_container_get(II $p, $k) {/** @var string $j */ return
	!($j = $p->getAdditionalInformation($k)) ? [] : df_json_decode($j)
;}

/**
 * 2017-01-19
 * @used-by \Df\Payment\W\Strategy\Refund::_handle()
 * https://github.com/mage2pro/core/blob/1.12.16/StripeClone/WebhookStrategy/Charge/Refunded.php?ts=4#L21-L23
 * @param II|I|OP|QP $p
 * @param string $k
 * @param string $v
 * @return bool
 */
function dfp_container_has(II $p, $k, $v) {return in_array($v, dfp_container_get($p, $k));}

/**
 * 2016-05-07
 * https://mage2.pro/t/1558
 * @param int $id
 * @return IOP|OP
 */
function dfp_get($id) {return dfp_r()->get($id);}

/**
 * 2016-08-08
 * @used-by \Df\Payment\Charge::iia()
 * @used-by \Df\Payment\Method::iia()
 * @used-by \Df\Payment\Token::get()
 * @param II|OP|QP|O|Q $p
 * @param mixed ...$k  [optional]
 * @return mixed|array(string => mixed)
 */
function dfp_iia($p, ...$k) {$p = dfp($p); return
	!($k = dfa_flatten($k)) ? $p->getAdditionalInformation() : (
		1 === count($k) ? $p->getAdditionalInformation($k[0]) :
			dfa_select_ordered($p->getAdditionalInformation(), $k)
	)
;}

/**
 * 2016-11-17
 * @used-by df_trans_is_test()
 * @param II|OP|QP $p
 * @return bool
 */
function dfp_is_test(II $p) {return dfp_iia($p, M::II__TEST);}

/**
 * 2016-08-26
 * @used-by \Dfe\SecurePay\Method::amountFormat()
 * @param float|int|string $a
 * @return string
 */
function dfp_last2($a) {return substr(strval(round(100 * df_float($a))), -2);}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @used-by \Df\Payment\Observer\FormatTransactionId::execute()
 * @param IM|II|OP|QP|T|object|string|O|null $v
 * @return bool
 */
function dfp_my($v) {return $v && dfpm($v) instanceof M;}

/**
 * 2016-05-07
 * https://mage2.pro/tags/order-payment-repository
 * @return IRepository|Repository
 */
function dfp_r() {return df_o(IRepository::class);}

/**
 * 2016-08-14
 * 2017-01-06
 * Эта функция устанавливает платежу специальный временный флаг
 * (этот флаг присутствует на протяжении обработки текущего запроса HTTP,
 * но не сохраняется в базе данных), который в последующем говорит платёжному модулю о том,
 * что инициатором данной платёжной транзакции является платёжная система
 * (как правильно — это либо действия работника магазина
 * в личном кабинете магазина в платёжной системы,
 * либо асинхронное уведомление платёжной системы о статусе обработки ею платежа,
 * либо действия покупателя в случае оффлайнового способа оплаты),
 * а не Magento (не действия покупателя в магазине
 * и не действия работника магазина в административной части Magento).
 *
 * В такой ситуации модуль должен выполнять лишь ту часть платёжной операции,
 * которая относится к Magento, но модуль не должен запрашивать выполнение этой операции
 * на стороне платёжной системы, потому что на стороне платёжной системы
 * эта операция уже выполнена, и платёжная система как раз нас об этом уведомляет.
 * @used-by \Df\Payment\W\Nav::op()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\Stripe\W\Strategy\Charge3DS::_handle()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::op()
 * @see \Df\Payment\Method::action()
 * @param II|OP|QP|null $p [optional]
 * @param bool $v [optional]
 * 2017-03-17
 * Если не указать = null, то null не будет допустимым аргументом: https://3v4l.org/mW5Lm
 * А если указать — то будет: https://3v4l.org/1d6Gq
 * @return II|OP|QP|null
 */
function dfp_webhook_case(II $p = null, $v = true) {if ($p) {$p[M::WEBHOOK_CASE] = $v;} return $p;}
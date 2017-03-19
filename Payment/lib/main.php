<?php
use Df\Payment\Method as M;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-05-20
 * @see df_ci_add()
 * @used-by \Df\Payment\Method::iiaAdd()
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
 * 2016-08-19
 * @see df_trans_by_payment()
 * @param T $t
 * @return OP
 */
function dfp_by_t(T $t) {return dfp_get($t->getPaymentId());}

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
function dfp_container_add(II $p, $k, $v) {$p->setAdditionalInformation($k, df_json_encode_pretty(
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
 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::_handle()
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
 * @param II|OP|QP $p
 * @param string|string[]|null $keys  [optional]
 * @return mixed|array(string => mixed)
 */
function dfp_iia(II $p, $keys = null) {
	/** @var mixed|array(string => mixed) $result */
	if (is_null($keys)) {
		$result = $p->getAdditionalInformation();
	}
	else {
		if (!is_array($keys)) {
			$keys = df_tail(func_get_args());
		}
		$result =
			1 === count($keys)
			? $p->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($p->getAdditionalInformation(), $keys)
		;
	}
	return $result;
}

/**
 * 2016-08-19
 * @see df_trans_is_my()
 * @param II|OP|QP $p
 * @return bool
 */
function dfp_is_my(II $p) {return dfpm_is_my($p->getMethodInstance());}

/**
 * 2016-11-17
 * @used-by df_trans_is_test()
 * @param II|OP|QP $p
 * @return bool
 */
function dfp_is_test(II $p) {return dfp_iia($p, M::II__TEST);}

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
 * @used-by \Df\Payment\W\Nav::ii()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::payment()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::payment()
 * @see \Df\Payment\Method::action()
 * @param II|OP|QP|null $p [optional]
 * 2017-03-17
 * Если не указать = null, то null не будет допустимым аргументом: https://3v4l.org/mW5Lm
 * А если указать — то будет: https://3v4l.org/1d6Gq
 * @return II|OP|QP|null
 */
function dfp_webhook_case(II $p = null) {if ($p) {$p[M::WEBHOOK_CASE] = true;} return $p;}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @param II|OP|QP|null $p
 * @param array(string => mixed) $v
 * @return void
 */
function dfp_set_transaction_info(II $p, array $v) {$p->setTransactionAdditionalInfo(
	T::RAW_DETAILS, df_ksort($v)
);}
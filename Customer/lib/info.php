<?php
use Df\Customer\Setup\UpgradeSchema as Schema;
use Magento\Customer\Model\Customer as C;
use Magento\Framework\DataObject;

/**
 * 2016-08-22
 * @see dfp_add_info()
 * 2016-08-23
 * Если значением ключа в $info будет null, то предыдущий ключ удалится: @see dfo()    
 * 2017-02-09
 * @used-by df_ci_save()
 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
 * @param DataObject|C $c
 * @param array(string => mixed) $info
 */
function df_ci_add(DataObject $c, array $info) {
	$c[Schema::F__DF] = df_json_encode(df_extend(df_eta(df_ci_get(null, $c)), $info));
}

/**
 * 2016-08-22
 * 2017-02-09
 * Эта функция используется нами в сценарии извлечения наших нестандартных данных из объекта-покупателя.
 * На сегодняшний день мы сохраняем для покупателя его идентификаторы в различных платёжных системах
 * (Stripe, Omise, Paymill), чтобы при повторных платежах покупатель мог использовать
 * ранее уже использованные им банковские карты без повторного ввода их реквизитов.
 * @used-by df_ci_add()
 * @param string|object|null $m [optional]
 * @param DataObject|C|null $o [optional]
 * @return string|array(string => mixed)|null
 */
function df_ci_get($m = null, DataObject $o = null) {
	/**
	 * 2017-05-22
	 * В сценарии регистрации гостевого покупателя после размещения им заказа
	 * $o является объектом примитивного класса @see DataObject, и у этого объкта нет идентификатора.
	 * https://mage2.pro/t/3941
	 */
	$o = df_customer($o) ?: $o;
	return !$o ? null : dfak(df_eta(df_json_decode($o[Schema::F__DF])), !$m ? null : df_class_second_lc($m));
}

/**
 * 2016-08-24
 * 2017-02-09
 * Эта функция используется нами в сценарии сохранения наших нестандартных данных для покупателя.
 * На сегодняшний день мы сохраняем для покупателя его идентификаторы в различных платёжных системах
 * (Omise, Paymill, Square, Stripe), чтобы при повторных платежах покупатель мог использовать
 * ранее уже использованные им банковские карты без повторного ввода их реквизитов.
 * @param string|object $m
 * @param mixed|null $info
 * @param C|null $c [optional]
 */
function df_ci_save($m, $info, C $c = null) {
	$data = [df_class_second_lc($m) => $info]; /** @var array(string => string) $data */
	if ($c = df_customer($c)) {
		df_ci_add($c, $data);
		// 2016-08-22
		// Сохранять покупателя надо обязательно,
		// потому что при оформлении заказа зарегистрированным ранее покупателем
		// его учётная запись не пересохраняется.
		df_eav_partial_save($c);
	}
	else {
		/**
		 * 2017-02-09
		 * Покупатель ещё не зарегистрирован.
		 * Сохраняем данные пока в сессию.
		 * Затем эти данные будут записаны в свежезарегистрированноого покупателя
		 * в методе @see \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
		 */
		df_checkout_session()->setDfCustomer($data);
	}
}
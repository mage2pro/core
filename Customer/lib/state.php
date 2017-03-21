<?php
use Magento\Customer\Api\AccountManagementInterface as IAM;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Sales\Model\Order as O;

/**
 * 2016-12-04
 * @param C|DC|int $c
 * @return bool
 */
function df_customer_is_need_confirm($c) {return
	IAM::ACCOUNT_CONFIRMATION_REQUIRED === df_customer_am()->getConfirmationStatus(df_customer_id($c))
;}

/**
 * 2016-03-15
 * How to programmatically check whether a customer is new or returning? https://mage2.pro/t/1617
 * @param int|null $id
 * @return bool
 */
function df_customer_is_new($id) {return dfcf(function($id) {return !$id || !df_conn()->fetchOne(
	df_db_from('sales_order', 'COUNT(*)')
		->where('? = customer_id', $id)
		->where('state IN (?)', [O::STATE_COMPLETE, O::STATE_PROCESSING])
);}, [$id]);}

/**
 * 2015-11-09
 * Сегодня заметил странную ситуацию, что метод @uses \Magento\Customer\Model\Session::isLoggedIn()
 * для авторизованных посетителей стал почему-то возвращать false
 * в контексте вызова из @used-by \Df\Sso\Button::_toHtml()().
 * Также заметил, что стандартный блок авторизации в шапке страницы
 * определяет авторизованность посетителя совсем по-другому алгоритму:
 * @see \Magento\Customer\Block\Account\AuthorizationLink::isLoggedIn()
 * Вот именно этот алгоритм мы сейчас и задействуем.
 * @return bool
 */
function df_customer_logged_in() {return
	df_customer_session()->isLoggedIn() || df_customer_logged_in_2()
;}

/**
 * 2015-11-09
 * Этот способ определения авторизованности посетителя
 * использует стандартный блок авторизации в шапке страницы:
 * @see \Magento\Customer\Block\Account\AuthorizationLink::isLoggedIn()
 * @return bool
 */
function df_customer_logged_in_2() {return df_http_context()->getValue(Context::CONTEXT_AUTH);}

/**
 * 2016-12-04
 * @return bool
 */
function df_is_login() {return df_handle('customer_account_login');}

/**
 * 2016-12-02
 * @return bool
 */
function df_is_reg() {return df_handle('customer_account_create');}
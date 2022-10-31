<?php
use Magento\Customer\Api\AccountManagementInterface as IAM;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Sales\Model\Order as O;

/**
 * 2016-12-04
 * @used-by \Df\Customer\Observer\RegisterSuccess::execute()
 * @param C|DC|int $c
 */
function df_customer_is_need_confirm($c):bool {return
	IAM::ACCOUNT_CONFIRMATION_REQUIRED === df_customer_am()->getConfirmationStatus(df_customer_id($c))
;}

/**
 * 2016-03-15 How to programmatically check whether a customer is new or returning? https://mage2.pro/t/1617
 * @used-by \Df\Payment\Init\Action::preconfigured()
 * @used-by \Df\Payment\Settings\_3DS::disable_()
 * @used-by \Df\Payment\Settings\_3DS::enable_()
 * @used-by \Dfe\CheckoutCom\Settings::actionDesired()
 * @param int|null $id
 */
function df_customer_is_new($id):bool {return dfcf(function($id) {return !$id || !df_conn()->fetchOne(
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
 * https://github.com/sayitwithagift/core/issues/3
 * @used-by \Df\Sso\Button::_toHtml()
 * @used-by \Dfe\AmazonLogin\Button::jsOptions()
 * @used-by beverageuniverse.com: app/design/frontend/Magento/Beverage/Magento_Theme/templates/html/topmenu.phtml
 */
function df_customer_logged_in():bool {return df_customer_session()->isLoggedIn() || df_customer_logged_in_2();}

/**
 * 2015-11-09
 * Этот способ определения авторизованности посетителя использует стандартный блок авторизации в шапке страницы:
 * @see \Magento\Customer\Block\Account\AuthorizationLink::isLoggedIn()
 * @used-by df_customer_logged_in()
 */
function df_customer_logged_in_2():bool {return df_http_context()->getValue(Context::CONTEXT_AUTH);}
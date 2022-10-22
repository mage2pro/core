<?php
use Df\Backend\Model\Auth;
use Df\Customer\Model\Session as DfSessionC;
use Magento\Backend\Model\Auth\Session as SessionB;
use Magento\Customer\Model\Session as SessionC;
use Magento\User\Model\User;

/**
 * 2016-01-07
 * @used-by \Dfe\GoogleBackendLogin\Plugin\Backend\App\AbstractAction::beforeDispatch()
 */
function df_backend_auth():Auth {return df_o(Auth::class);}

/**
 * 2016-12-23
 * @used-by df_backend_user()
 * @used-by df_session()
 */
function df_backend_session():SessionB {return df_o(SessionB::class);}

/**
 * 2016-12-23
 * 2017-03-15
 * Мы не должны и не можем использовать здесь @see df_is_backend(), потому что эта функция использует нас!
 * Если мы не в административной части, то df_backend_session()->getUser() просто вернёт null.
 * @used-by df_is_backend()
 * @used-by df_sentry_m()
 * @used-by \MageSuper\Casat\Observer\ProductSaveBefore::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/73)
 * @return User|null
 */
function df_backend_user() {return df_backend_session()->getUser();}

/**
 * 2020-02-11
 * @used-by \Dfe\Sift\Session::get()
 * @return SessionC|DfSessionC|SessionB
 */
function df_session() {return df_is_backend() ? df_backend_session() : df_customer_session();}
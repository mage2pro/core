<?php
use Df\Backend\Model\Auth;
use Magento\Backend\Model\Auth\Session;
use Magento\User\Model\User;
/**
 * 2016-01-07
 * @return Auth
 */
function df_backend_auth() {return df_o(Auth::class);}
/**
 * 2016-12-23
 * @return Session
 */
function df_backend_session() {return df_o(Session::class);}
/**
 * 2016-12-23
 * 2017-03-15
 * Мы не должны и не можем использовать здесь @see df_is_backend(),
 * потому что эта функция использует нас!
 * Если мы не в административной части, то df_backend_session()->getUser() просто вернёт null.
 * @return User|null
 */
function df_backend_user() {return df_backend_session()->getUser();}
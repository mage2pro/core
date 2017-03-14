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
 * @return User|null
 */
function df_backend_user() {return !df_is_backend() ? null : df_backend_session()->getUser();}
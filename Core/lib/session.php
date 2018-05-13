<?php
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\Session\Generic as GenericSession;
use Magento\Framework\Session\SessionManagerInterface as ISessionManager;
/**
 * 2018-05-14
 * @used-by \Df\Backend\Model\Auth::loginByEmail()
 * @return ISessionManager|BackendSession|GenericSession
 */
function df_session_manager() {return df_o(ISessionManager::class);}
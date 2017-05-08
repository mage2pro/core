<?php
use Magento\Framework\Session\Config;
use Magento\Framework\Session\Config\ConfigInterface as IConfig;
/**
 * 2017-05-08
 * @used-by \Df\Framework\Plugin\Session\SessionManager::beforeStart()
 * @return Config|IConfig
 */
function df_session_config() {return df_o(IConfig::class);}
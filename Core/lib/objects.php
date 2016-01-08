<?php
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
/**
 * 2016-01-07
 * @return \Df\Backend\Model\Auth
 */
function df_backend_auth() {return df_o(\Df\Backend\Model\Auth::class);}

/** @return \Magento\Framework\Encryption\EncryptorInterface|\Magento\Framework\Encryption\Encryptor */
function df_encryptor() {return df_o(\Magento\Framework\Encryption\EncryptorInterface::class);}

/** @return \Magento\Framework\Message\ManagerInterface|\Magento\Framework\Message\Manager*/
function df_message() {return df_o(\Magento\Framework\Message\ManagerInterface::class);}

/**
 * 2016-01-06
 * @return Config|ConfigInterface
 */
function df_wysiwyg_config() {return df_o(ConfigInterface::class);}

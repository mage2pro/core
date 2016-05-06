<?php
/** @return \Magento\Framework\Encryption\EncryptorInterface|\Magento\Framework\Encryption\Encryptor */
function df_encryptor() {return df_o(\Magento\Framework\Encryption\EncryptorInterface::class);}

/**
 * https://mage2.pro/t/974
 * @return \Magento\Framework\Message\ManagerInterface|\Magento\Framework\Message\Manager
 */
function df_message() {return df_o(\Magento\Framework\Message\ManagerInterface::class);}


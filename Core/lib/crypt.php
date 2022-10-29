<?php
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\EncryptorInterface as IEncryptor;

/**
 * @used-by \Df\Config\Settings::p()
 * @return IEncryptor|Encryptor
 */
function df_encryptor() {return df_o(IEncryptor::class);}
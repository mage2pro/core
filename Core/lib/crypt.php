<?php
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\EncryptorInterface as IEncryptor;

/** @return IEncryptor|Encryptor */
function df_encryptor() {return df_o(IEncryptor::class);}
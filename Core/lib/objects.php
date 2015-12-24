<?php
/** @return \Magento\Framework\Encryption\EncryptorInterface|\Magento\Framework\Encryption\Encryptor */
function df_encryptor() {return df_o(\Magento\Framework\Encryption\EncryptorInterface::class);}

/** @return \Magento\Framework\Message\ManagerInterface|\Magento\Framework\Message\Manager*/
function df_message() {return df_o(\Magento\Framework\Message\ManagerInterface::class);}

/** @return array(string => mixed) */
function df_wysiwyg_config() {
	/** @var \Magento\Cms\Model\Wysiwyg\Config $o */
	$o = df_o(\Magento\Cms\Model\Wysiwyg\Config::class);
	return $o->getConfig()->getData();
}



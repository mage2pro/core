<?php
/**
 * 2022-11-24
 * The @see DS constant exists in Magento 1: https://github.com/OpenMage/magento-mirror/blob/1.9.4.5/app/Mage.php#L27
 * It is absent in Magento 2.
 * It is also absent in PHP: https://3v4l.org/FTR0R
 * @used-by \Df\Qa\Failure\Error::info()
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
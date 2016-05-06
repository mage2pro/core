<?php
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
/**
 * 2016-01-06
 * @return Config|ConfigInterface
 */
function df_wysiwyg_config() {return df_o(ConfigInterface::class);}
<?php
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
/**
 * 2016-01-06
 * @used-by Df\Widget\P\Wysiwyg::prepareElementHtml() (https://github.com/mage2pro/core/issues/392)
 * @used-by Dfe\Markdown\FormElement::config()
 * @used-by Dfe\Markdown\FormElement::enabled()
 * @return Config|ConfigInterface
 */
function df_wysiwyg_config() {return df_o(ConfigInterface::class);}
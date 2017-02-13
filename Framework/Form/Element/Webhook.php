<?php
namespace Df\Framework\Form\Element;
/**
 * 2016-05-30
 * @final We are unable to specify it explicitly with the «final» PHP keyword
 * because Magento 2 will autogenerate a subclass:
 * https://github.com/magento/devdocs/blob/713b78a/guides/v2.0/extension-dev-guide/code-generation.md#when-is-code-generated
 */
class Webhook extends Url {
	/**
	 * 2016-05-31
	 * @override
	 * @see \Df\Framework\Form\Element\Url::messageForThirdPartyLocalhost()
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	protected function messageForThirdPartyLocalhost() {return
		'The notifications are not available, because the store is running on <b>localhost</b>'
	;}
}
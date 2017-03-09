<?php
namespace Df\Framework\Form\Element;
// 2016-05-30
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
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
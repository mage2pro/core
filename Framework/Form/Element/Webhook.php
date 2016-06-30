<?php
namespace Df\Framework\Form\Element;
// 2016-05-30
class Webhook extends Url {
	/**
	 * 2016-05-31
	 * @override
	 * @see \Df\Framework\Form\Element\Url::messageForThirdPartyLocalhost()
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	protected function messageForThirdPartyLocalhost() {
		return 'The notifications are not available, because the store is running on <b>localhost</b>';
	}
}
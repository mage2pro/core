<?php
namespace Df\Payment\FE;
/**
 * 2017-04-12
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * The class is used for some payment system providers
 * who work with customer redirection and do not allow to specify the customer return URL
 * dynamically, so the customer return URL should be specified in the PSP's merchant interface.
 * For now, it is used only by my Robokassa extension.
 */
class CustomerReturn extends \Df\Framework\Form\Element\Url {
	/**
	 * 2017-04-12
	 * @override
	 * @see \Df\Framework\Form\Element\Url::url()
	 * @used-by \Df\Framework\Form\Element\Url::messageForOthers()
	 * @return string
	 */
	final protected function url() {return dfp_url_customer_return_remote($this->m());}
}
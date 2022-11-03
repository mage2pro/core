<?php
use Magento\Framework\App\Response\RedirectInterface as IResponseRedirect;
use Magento\Store\App\Response\Redirect as ResponseRedirect;

/**
 * 2017-11-17
 * @used-by \Df\Payment\W\Action::execute()
 */
function df_is_redirect():bool {return df_response()->isRedirect();}

/**
 * 2017-11-16
 * I implemented it by analogy with @see \Magento\Framework\App\Action\Action::_redirect():
 *		protected function _redirect($path, $arguments = []) {
 *			$this->_redirect->redirect($this->getResponse(), $path, $arguments);
 *			return $this->getResponse();
 *		}
 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L159-L170
 * @used-by df_redirect_to_checkout()
 * @used-by df_redirect_to_home()
 * @used-by df_redirect_to_payment()
 * @used-by df_redirect_to_success()
 * @param string $path
 * @param array(string => mixed) $p [optional]
 * @return IResponseRedirect|ResponseRedirect
 */
function df_redirect($path, $p = []) {
	$r = df_response_redirect(); /** @var IResponseRedirect|ResponseRedirect $r */
	/**
	 * 2017-11-17
	 * @uses \Magento\Framework\App\Response\Http::setRedirect():
	 *		public function setRedirect($url, $code = 302) {
	 *			$this
	 *				->setHeader('Location', $url, true)
	 *				->setHttpResponseCode($code)
	 *			;
	 *			return $this;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/HTTP/PhpEnvironment/Response.php#L113-L122
	 *
	 * We can then check whether a redirect is set using
	 * @see \Magento\Framework\HTTP\PhpEnvironment\Response::isRedirect():
	 *		public function isRedirect() {
	 *			return $this->isRedirect;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/HTTP/PhpEnvironment/Response.php#L162-L170
	 *
	 * It does work because of the
	 * @see \Magento\Framework\HTTP\PhpEnvironment\Response::setHttpResponseCode() implementation:
	 * 		 $this->isRedirect = (300 <= $code && 307 >= $code) ? true : false;
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/HTTP/PhpEnvironment/Response.php#L124-L137
	 */
	$r->redirect(df_response(), $path, $p);
	return $r;
}

/**
 * 2019-11-21
 * @used-by \RWCandy\Captcha\Observer\CustomerAccountCreatePost::execute()
 */
function df_redirect_back():void {df_response()->setRedirect(df_response_redirect()->getRefererUrl());}

/**
 * 2020-05-27
 * @used-by \BlushMe\Checkout\Observer\ControllerActionPredispatch\CheckoutCartIndex::execute()
 */
function df_redirect_to_checkout():void {df_redirect('checkout');}

/**
 * 2020-10-20
 * @used-by \BlushMe\Checkout\Observer\ControllerActionPredispatch\CheckoutCartIndex::execute()
 */
function df_redirect_to_home():void {df_redirect('/');}

/**
 * 2017-11-17
 * 2018-12-17
 * I have added the @uses df_order_last() condition
 * because otherwise the Yandex.Kassa payment module does not return a proper response to PSP.
 * 2019-07-04
 * The previous (2018-12-17) strange commit: https://github.com/mage2pro/core/commit/3eb6c1d2
 * Currtently, I do not understand it.
 * And it brokes the proper handling of PSP errors:
 * https://mail.google.com/mail/u/0/#inbox/FMfcgxwChSGRJmWZrRBLWpcnXKbQZvjw
 * So I have reverted the code for `df_redirect_to_payment`,
 * but preserved it for @see df_redirect_to_success()
 * @used-by \Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 */
function df_redirect_to_payment():void {df_redirect('checkout', ['_fragment' => 'payment']);}

/**
 * 2017-11-17
 * 2018-12-17
 * I have added the @uses df_order_last() condition
 * because otherwise the Yandex.Kassa payment module does not return a proper response to PSP.
 * @used-by \Alignet\Paymecheckout\Controller\Classic\Response::execute() (innomuebles.com, https://github.com/innomuebles/m2/issues/11)
 * @used-by \Df\Payment\CustomerReturn::execute()
 * @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 */
function df_redirect_to_success():void {df_order_last(false) ? df_redirect('checkout/onepage/success') : null;}

/**
 * 2019-11-21
 * @used-by df_redirect()
 * @used-by df_redirect_back()
 * @return IResponseRedirect|ResponseRedirect
 */
function df_response_redirect() {return df_o(IResponseRedirect::class);}
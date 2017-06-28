<?php
namespace Df\OAuth;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2017-06-27
 * @see \Df\Sso\CustomerReturn
 * @see \Dfe\Dynamics365\Controller\Adminhtml\OAuth\Index
 */
abstract class ReturnT extends _P {
	/**
	 * 2017-06-27
	 * @used-by execute()
	 * @see \Df\Sso\CustomerReturn::_execute()
	 * @see \Dfe\Dynamics365\Controller\Adminhtml\OAuth\Index::_execute()
	 */
	abstract protected function _execute();

	/**
	 * 2017-06-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see _P::execute()
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	function execute() {
		try {
			$this->_execute();
		}
		catch (\Exception $e) {
			df_log($e);
			df_message_error($e);
		}
		$this->postProcess();
		return $this->resultRedirectFactory->create()->setUrl($this->redirectUrl());
	}
	
	/**
	 * 2016-06-06
	 * @used-by execute()
	 * @see \Dfe\AmazonLogin\Controller\Index\Index::postProcess()
	 */
	protected function postProcess() {}

	/**
	 * 2016-06-05 @see urldecode() здесь вызывать уже не надо, проверял.
	 * 2016-12-02
	 * Если адрес для перенаправления покупателя передётся в адресе возврата,
	 * то адрес для перенаправления там може быть закодирован посредством @see base64_encode()
	 * @see \Dfe\BlackbaudNetCommunity\Url::get()
	 * @used-by execute()
	 * @see \Dfe\Dynamics365\Controller\Adminhtml\OAuth\Index::redirectUrl()
	 * @return string
	 */
	protected function redirectUrl() {return
		df_starts_with($r = df_request($this->redirectUrlKey()) ?: df_url(), 'http') ? $r :
			base64_decode($r)
	;}

	/**
	 * 2016-06-04
	 * @used-by redirectUrl()
	 * @see \Dfe\AmazonLogin\Controller\Index\Index::redirectUrlKey()
	 * @return string
	 */
	protected function redirectUrlKey() {return self::REDIRECT_URL_KEY;}

	/**
	 * 2016-12-02
	 * @used-by redirectUrlKey()
	 * @used-by \Dfe\BlackbaudNetCommunity\Url::get()
	 */
	const REDIRECT_URL_KEY = 'url';
}
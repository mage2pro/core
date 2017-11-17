<?php
namespace Df\OAuth;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2017-06-27
 * @see \Df\OAuth\ReturnT\GeneralPurpose
 * @see \Df\Sso\CustomerReturn
 */
abstract class ReturnT extends _P {
	/**
	 * 2017-06-27
	 * @used-by execute()
	 * @see \Df\Sso\CustomerReturn::_execute()
	 */
	abstract protected function _execute();

	/**
	 * 2017-06-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return \Magento\Framework\Controller\Result\Redirect
	 */
	function execute() {
		try {$this->_execute();}
		catch (\Exception $e) {df_log($e); df_message_error($e);}
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
	 * @used-by \Df\Sso\CustomerReturn::redirectUrl()
	 * @see \Df\OAuth\ReturnT\GeneralPurpose::redirectUrl()
	 * @see \Df\Sso\CustomerReturn::redirectUrl()
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
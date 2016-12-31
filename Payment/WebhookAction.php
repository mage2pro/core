<?php
// 2016-08-27
namespace Df\Payment;
use Df\Payment\Webhook as W;
use Df\Payment\Settings as S;
class WebhookAction extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Df\Framework\Controller\Result\Text
	 */
	public function execute() {
		try {
			/** @var W $r */
			/** @uses Webhook::i() */
			$r = $this->rCallS('i', $this->additionalParams() + $_REQUEST);
			$result = $r->handle();
		}
		catch (\Exception $e) {
			/** @uses Webhook::resultError() */
			$result = $this->rCallS('resultError', $e);
		}
		return $result;
	}

	/**
	 * 2016-08-27
	 * @used-by \Dfe\AllPay\Controller\Confirm\Index::execute()
	 * @return array(string => string)
	 */
	protected function additionalParams() {return [];}

	/**
	 * 2016-08-27
	 * @param string $method
	 * @param mixed[] ...$params [optional]
	 * @return string
	 */
	private function rCallS($method, ...$params) {return df_con_s($this, 'Webhook', $method, $params);}
}
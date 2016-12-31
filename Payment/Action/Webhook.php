<?php
// 2016-08-27
namespace Df\Payment\Action;
use Df\Payment\Webhook as W;
use Df\Payment\Settings as S;
class Webhook extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Df\Framework\Controller\Result\Text
	 */
	public function execute() {
		try {
			/** @var W $r */
			/** @uses \Df\Payment\Webhook::i() */
			$r = $this->rCallS('i', $this->additionalParams() + $_REQUEST);
			$result = $r->handle();
		}
		catch (\Exception $e) {
			/** @uses \Df\Payment\Webhook::resultError() */
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
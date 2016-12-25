<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\R\Response as R;
use Df\Payment\Settings as S;
class Confirm extends \Df\Payment\R\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Df\Framework\Controller\Result\Text
	 */
	public function execute() {
		if (self::needLog()) {
			dfp_report($this, $_REQUEST, 'сonfirmation');
		}
		try {
			/** @var R $r */
			/** @uses \Df\Payment\R\Response::i() */
			$r = $this->rCallS('i', $this->additionalParams() + $_REQUEST);
			$result = $r->handle();
		}
		catch (\Exception $e) {
			/** @uses \Df\Payment\R\Response::resultError() */
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
	private function rCallS($method, ...$params) {return df_con_s($this, 'Response', $method, $params);}
}
<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\R\Response as R;
class Confirm extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return \Df\Framework\Controller\Result\Text
	 */
	public function execute() {
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
	 * @return string
	 */
	private function rC() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_con($this, 'Response');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-27
	 * @param string $method
	 * @param mixed[] ...$params [optional]
	 * @return string
	 */
	private function rCallS($method, ...$params) {
		return call_user_func_array([$this->rC(), $method], $params);
	}
}
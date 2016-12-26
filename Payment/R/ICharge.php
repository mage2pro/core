<?php
namespace Df\Payment\R;
// 2016-08-29
// Позволяет сделать статический метод абстрактным: http://stackoverflow.com/a/6386309
interface ICharge {
	/**
	 * 2016-08-29
	 * @used-by \Df\Payment\R\Charge::p()
	 * @used-by \Df\Payment\Webhook\Response::requestId()
	 * @return string
	 */
	static function requestIdKey();
}
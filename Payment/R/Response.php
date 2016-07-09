<?php
namespace Df\Payment\R;
// 2016-07-09
// Портировал из Российской сборки Magento.
abstract class Response {
	/** @return string */
	abstract public function transactionType();
	/** @return bool */
	abstract protected function isSuccessful();
	/** @return string */
	abstract protected function message();
}
<?php
namespace Df\Framework\W;
/**
 * 2017-09-12
 * @see \Df\Framework\W\Result\Text
 * @see \Dfe\Qiwi\Result
 * @see \Dfe\YandexKassa\Result
 */
abstract class Result extends AbstractResult {
	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @see \Df\Framework\W\Result\Text::__toString()
	 * @see \Dfe\Qiwi\Result::__toString()
	 * @return string
	 */
	abstract function __toString();
}
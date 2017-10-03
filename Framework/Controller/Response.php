<?php
namespace Df\Framework\Controller;
/**
 * 2017-09-12
 * @see \Df\Framework\Controller\Response\Text
 * @see \Dfe\Qiwi\Response
 * @see \Dfe\YandexKassa\Response
 */
abstract class Response extends AbstractResult {
	/**
	 * 2017-03-30
	 * @used-by \Df\Payment\W\Action::execute()
	 * @see \Df\Framework\Controller\Response\Text::__toString()
	 * @see \Dfe\Qiwi\Response::__toString()
	 * @return string
	 */
	abstract function __toString();
}
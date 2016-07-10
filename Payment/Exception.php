<?php
namespace Df\Payment;
// 2016-07-09
class Exception extends \Df\Core\Exception {
	/**
	 * 2016-07-09
	 * Если метод вернёт true, то система добавит к сообщению обрамление/пояснение.
	 * @return bool
	 */
	public function needFraming() {return true;}
}



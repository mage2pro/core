<?php
namespace Df\Payment;
/**
 * 2017-03-20
 * Этот интерфейс нужен нам для определения модуля текущего класса
 * и требуемых классов внутри этого модуля или в родительских модулях
 * посредством функций типа @see df_con(), df_con_heir(), df_con_hier() и т.п.
 * Раньше я для этой цели использовал текущий класс,
 * однако сейчас я стал применять технологию виртуальных классов (virtualType),
 * а в этом случае реальный класс будет отличаться от виртуального.
 * @see Operation
 * @see \Df\Payment\Operation\ISource
 * @see \Df\Payment\W\Handler
 */
interface IMA {
	/**
	 * 2017-03-20
	 * @used-by \Df\PaypalClone\Signer::_sign()
	 * @used-by Operation::m()
	 * @see Operation::m()
	 * @see \Df\Payment\Operation\Source\Order::m()
	 * @return Method
	 */
	function m();
}
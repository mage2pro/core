<?php
// 2016-08-27
namespace Df\Payment\R;
use Magento\Sales\Model\Order\Payment as OP;
abstract class Charge extends \Df\Payment\Charge implements ICharge {
	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Charge::p()
	 * @return array(string => mixed)
	 */
	abstract protected function params();

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\Charge::p()
	 * @return string
	 */
	abstract protected function signatureKey();

	/**
	 * 2016-08-29
	 * @used-by \Df\Payment\R\Charge::p()
	 * @return string
	 */
	protected function requestId() {return $this->oii();}

	/**
	 * 2016-08-27
	 * @param Method $method
	 * @return array(string, array(string => mixed))
	 */
	final public static function p(Method $method) {
		/** @var self $i */
		$i = df_create(df_con($method, 'Charge'), [self::$P__METHOD => $method]);
		/**
		 * 2016-08-29
		 * Метод @uses \Df\Payment\R\ICharge::requestIdKey(),
		 * но мы вызываем его нестатично (чтобы он был вызван для нужного класса, а не бля базового),
		 * и это успешно работает безо всяких предупреждений интерпретатора:
		 * https://3v4l.org/N2VD2
		 * http://stackoverflow.com/a/32746909
		 * http://stackoverflow.com/a/15756165
		 * Некоторые утверждают, что якобы на старых версиях PHP
		 * это может выдавать предупреждение уровня E_STRICT:
		 * http://stackoverflow.com/a/12874405
		 * Однако это неправда, я проверил: https://3v4l.org/1JY8i
		 */
		/** @var string $id */
		$id = $i->requestId();
		df_assert_string_not_empty($id);
		/** @var array(string => mixed) $p */
		$p = [$i->requestIdKey() => $id] + $i->params();
		return [$id, $p + [$i->signatureKey() => Signer::signRequest($i, $p)]];
	}
}
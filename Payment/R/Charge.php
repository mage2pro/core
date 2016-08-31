<?php
// 2016-08-27
namespace Df\Payment\R;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
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
	 * 2016-08-27
	 * @param string $path [optional]
	 * @return string
	 */
	protected function callback($path = 'confirm') {return df_url_callback($this->route($path));}

	/**
	 * 2016-08-27
	 * @return string
	 */
	protected function customerReturn() {return df_url_frontend($this->route('customerReturn'));}

	/**
	 * 2016-08-27
	 * Этот метод решает 2 проблемы, возникающие при работе на localhost:
	 * 1) Некоторые способы оплаты (SecurePay) вообще не позвлояют указывать локальные адреса.
	 * 2) Некоторые способы оплаты (allPay) допускают локальный адрес возврата,
	 * но для тестирования его нам использовать нежелательно,
	 * потому что сначала вручную сэмулировать и обработать callback.
	 * @return string
	 */
	protected function customerReturnRemote() {return $this->callback('customerReturn');}

	/**
	 * 2016-08-29
	 * @used-by \Df\Payment\R\Charge::p()
	 * @return string
	 */
	protected function requestId() {return $this->o()->getIncrementId();}

	/**
	 * 2016-08-27
	 * @param string $path
	 * @return string
	 */
	private function route($path){
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_route($this);
		}
		return df_cc_path($this->{__METHOD__}, $path);
	}

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
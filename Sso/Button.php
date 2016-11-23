<?php
// 2016-11-23
namespace Df\Sso;
use Df\Core\Settings as S;
use Magento\Framework\View\Element\AbstractBlock;
abstract class Button extends AbstractBlock {
	/**
	 * 2016-11-23
	 * @used-by _toHtml()
	 * @return string
	 */
	abstract protected function loggedOut();

	/**
	 * 2016-11-23
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		if (!self::s()->enable()) {
			$result = '';
		}
		else if (df_customer_logged_in()) {
			$result = $this->loggedIn();
		}
		else {
			$result = $this->loggedOut();
		}
		return $result;
	}

	/**
	 * 2016-11-23
	 * @used-by _toHtml()
	 * @return string
	 */
	protected function loggedIn() {return '';}

	/**
	 * 2016-11-23
	 * Для каждого класса кнопок система будет создавать несколько экземпляров:
	 * для каждого из местоположений кнопки.
	 * Поэтому кэшируем результат вызова @uses \Df\Core\Settings::convention() для класса
	 * (но при этом используем static::class, чтобы разные классы имели разные значения кэша).
	 * @return S
	 */
	private static function s() {return dfcf(function() {return S::convention(static::class);});}
}
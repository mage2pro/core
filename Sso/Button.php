<?php
// 2016-11-23
namespace Df\Sso;
use Df\Config\Settings as SModule;
use Df\Sso\Settings\Button as S;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Html\Links;
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
		if (!self::sModule()->enable() || !$this->s()->enable()) {
			$result = '';
		}
		else if (df_customer_logged_in()) {
			$result = $this->loggedIn();
		}
		else {
			$result = $this->loggedOut();
		}
		/**
		 * 2016-11-23
		 * Ссылки в шапке надо обязательно обрамлять в <li>, потому что они выводятся внутри <ul>:
				$html = '<ul' . ($this->hasCssClass() ? ' class="' . $this->escapeHtml(
					$this->getCssClass()
				) . '"' : '') . '>';
				foreach ($this->getLinks() as $link) {
					$html .= $this->renderLink($link);
				}
				$html .= '</ul>';
		 * @see \Magento\Framework\View\Element\Html\Links::_toHtml()
		 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/View/Element/Html/Links.php#L76-L82
		 */
		if ($result && $this->getParentBlock() instanceof Links) {
			$result = "<li>{$result}</li>";
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
	 * 2016-11-24
	 * @return S
	 */
	protected function s() {return dfc($this, function() {return new S($this['dfConfigPrefix']);});}

	/**
	 * 2016-11-23
	 * Для каждого класса кнопок система будет создавать несколько экземпляров:
	 * для каждого из местоположений кнопки.
	 * Поэтому кэшируем результат вызова @uses \Df\Config\Settings::convention() для класса
	 * (но при этом используем static::class, чтобы разные классы имели разные значения кэша).
	 * 2016-11-24
	 * Передаём static::class как аргумент, чтобы потомки этого класса имели индивидуальный кэш:
	 * https://github.com/mage2pro/core/blob/ab34df/Core/lib/cache.php?ts=4#L151-L160
	 * @return SModule
	 */
	private static function sModule() {return dfcf(function($c) {return
		SModule::convention($c)
	;}, [static::class]);}
}
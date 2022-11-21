<?php
namespace Df\Config\Plugin\Block\System\Config\Form;
use Df\Config\Fieldset as F;
use Df\Framework\Form\Element\Fieldset as FE;
use Magento\Config\Block\System\Config\Form\Fieldset as Sb;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Fieldset as FEM;
# 2015-12-13
# Хитрая идея, которая уже давно пришла мне в голову: наследуясь от модифицируемого класса,
# мы получаем возможность вызывать методы с областью доступа protected у переменной $s.
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Fieldset extends Sb {
	/**
	 * 2016-01-01
	 * An empty constructor allows us to skip the parent's one.
	 * Magento (at least at 2016-01-01) is unable to properly inject arguments into a plugin's constructor,
	 * and it leads to the error like: «Missing required argument $amount of Magento\Framework\Pricing\Amount\Base».
	 */
	function __construct() {}

	/**
	 * 2015-12-21
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * Цель перекрытия — устранения дефекта:
	 * «Magento 2 backend incorrectly renders the nested fieldsets: adds nested TR tags with the same id».
	 * https://mage2.pro/t/330
	 * Этот дефект приводит к неработоспособности условия <depends> для элемента:
	 * т.е. видимость элемента перестаёт зависеть от другой опции.
	 * @see \Magento\Config\Block\System\Config\Form\Fieldset::render()
	 */
	function aroundRender(Sb $sb, \Closure $f, AE $e):string {/** @var string $r */
		/**
		 * 2016-01-01
		 * Потомки @see \Magento\Config\Block\System\Config\Form\Fieldset могли перекрыть метод
		 * @see \Magento\Config\Block\System\Config\Form\Fieldset::render().
		 * Пример: @see \Magento\Config\Block\System\Config\Form\Fieldset\Modules\DisableOutput::render()
		 * Поэтому в случае с классом-потомком неправильно не вызывать метод render().
		 * 2018-04-19
		 * The previous code was:
		 * 		if (get_class($sb) !== df_interceptor(Sb::class)) {
		 * https://github.com/mage2pro/core/blob/3.6.29/Config/Plugin/Block/System/Config/Form/Fieldset.php#L37
		 * The `df_interceptor(F::class)` comparison is neeeded
		 * when we have a top-level `Df\Framework\Form\Element\ArrayT`, e.g.:
		 * https://github.com/mage2pro/currency-format/blob/1.0.24/etc/adminhtml/system.xml#L40-L51
		 */
		if (!in_array(get_class($sb), [df_interceptor(Sb::class), df_interceptor(F::class)])) {
			$r = $f($e);
		}
		else {
			$sb->setElement($e);
			$r = $sb->_getHeaderHtml($e);
			foreach ($e->getElements() as $f) {
				$r .= (
					$f instanceof FEM
					&& !$f instanceof FE # 2015-12-21 Вот в этой добавке и заключается суть модифицации.
					? df_tag('tr', ['id' => 'row_' . $f->getHtmlId()], df_tag('td', ['colspan' => 4], $f->toHtml()))
					: $f->toHtml()
				);
			}
			$r .= $sb->_getFooterHtml($e);
		}
		return $r;
	}
}
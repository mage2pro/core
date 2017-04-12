<?php
namespace Df\Config;
use Magento\Config\Block\System\Config\Form\Fieldset as CF;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Data\Form\Element\Fieldset as EF;
/**
 * 2016-07-01
 * Нельзя называть класс просто Extension, потому что такие имена классов
 * уже зарезервированы для одной из технологий Magento 2.
 *
 * К сожалению, мы не можем только для наших модулей использовать свой класс
 * вместо @see \Magento\Framework\Data\Form\Element\Fieldset,
 * поэтому вместо этого переопределяем frontend_model:
 * используем наш класс вместо класса @see \Magento\Config\Block\System\Config\Form\Fieldset
 * @method $this setElement(AE $value)
 */
class Fieldset extends CF {
	/**
	 * 2016-07-01
	 * @override
	 * @see \Magento\Config\Block\System\Config\Form\Fieldset::_getHeaderCommentHtml()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form/Fieldset.php#L166-L175
	 * @used-by \Magento\Config\Block\System\Config\Form\Fieldset::_getHeaderHtml()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form/Fieldset.php#L121
	 * @param AE|EF $e
	 * @return string
	 */
	protected function _getHeaderCommentHtml($e) {return !($m = df_fe_m($e, false))
		? parent::_getHeaderCommentHtml($e)
		: df_tag('div', 'comment', df_tag_ab(__('Have a question?'), df_package($m, 'homepage')))
	;}
}
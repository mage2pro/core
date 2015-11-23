<?php
namespace Df\Framework\Data\Form\Element;
use Magento\Framework\Data\Form\Element\AbstractElement as _AbstractElement;
/**
 * 2015-11-22
 * Пока что это класс используется только ради описания магазических методов в шапке.
 * @method string|null getClass()
 * @method string|null getContainerClass()
 * @method string|null getCssClass()
 * @method string|null getExtType()
 * @method string|null getFieldExtraAttributes()
 * @method string|null getNote()
 * @method bool|null getNoDisplay()
 * @method bool|null getNoWrapAsAddon()
 * @method bool|null getRequired()
 * @method string|null getScopeLabel()
 * @method AbstractElement setContainerClass(string $value)
 */
class AbstractElement extends _AbstractElement{
	/**
	 * 2015-11-23
	 * Я же хочу иметь возможность изначально скрывать вложенный fieldset
	 * и раскрывать его лишь по желанию администратора.
	 * Сначала я делал это через JavaScript:
	 * т.е. сначала fieldset видим, потом загружается JavaScript и скрывает его.
	 * Это выглядит не очень эстетично: fielset мелькает.
	 * Поэтому я добавляю здесь возможность изначально скрывать fieldset.
	 * Это можно было бы сделать добавлением соответствующего класса css.
	 */
	const CONTAINER_CLASS = 'df_container_class';
}



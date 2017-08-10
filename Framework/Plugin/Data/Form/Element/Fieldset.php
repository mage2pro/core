<?php
namespace Df\Framework\Plugin\Data\Form\Element;
use Df\Config\Backend as B;
use Df\Framework\Form\ElementI as El;
use Magento\Framework\Data\Form\Element\Fieldset as Sb;
// 2016-08-03
class Fieldset {
	/**
	 * 2016-08-03
	 * Начиная с Magento 2.1.0 backend model создаётся только если данные присутствуют в базе данных
	 * для конкретной области действия настроек (scope и scopeId).
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L309-L327
	 * Если данные отсутстствуют в БД для конкретной области действия настроек,
	 * то backend model вообще не создаётся,
	 * однако данные всё равно извлекаются из БД из общей области действия настроек:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L323-L327
	 * Видимо, такое поведение дефектно: данные могут попасть в форму
	 * в обход обработки и валидации их в backend model.
	 *
	 * Ранее (до версии 2.1.0) backend model создавалась в любом случае:
	 * такое поведение я считаю более верным:
	 * https://github.com/magento/magento2/blob/2.0.8/app/code/Magento/Config/Block/System/Config/Form.php#L330-L342
	 *
	 * В плагин мы попадаем отсюда: @see \Magento\Config\Block\System\Config\Form::_initElement()
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L347-L367
	 *
	 * @see \Magento\Framework\Data\Form\Element\Fieldset::addField()
	 * @param Sb|El $sb
	 * @param string $elementId
	 * @param string $type
	 * @param array(string => mixed) $config
	 * @param bool $after [optional]
	 * @param bool $isAdvanced [optional]
	 * @return array(string|bool|array(string => mixed))
	 */
	function beforeAddField(
		Sb $sb, $elementId, $type, array $config, $after = false, $isAdvanced = false
	) {
		$fc = dfa($config, 'field_config'); /** @var array(string => mixed)|null $fc */
		/**
		 * 2016-09-27
		 * Модуль Unirgy Dropship ошибочно пихает в $fc объект класса stdClass вместо массива:
		 * https://code.dmitry-fedyuk.com/m2e/stripe/issues/5
		 */
		if ($fc && is_array($fc)) {
			$path = df_cc_path(dfa($fc, 'path'), dfa($fc, 'id')); /** @var string|null $path */
			$backendC = dfa($fc, 'backend_model'); /** @var string|null $backendC */
			/**
			 * 2016-08-03
			 * Конкретное значение тега «type» из system.xml можно получить вызовом dfa($fc, 'type')
			 * Однако нам это не нужно: достаточно использовать аргумент $type:
			 * @see \Magento\Config\Model\Config\Structure\Element\Field::getType()
			 */
			$value = dfa($config, 'value'); /** @var mixed|null $value */
			if ($path && $backendC && !B::processed($path)
				&& is_a($type, El::class, true) && !is_null($value)
			) {
				/**
				 * 2016-08-03
				 * By analogy with @see \Magento\Config\Block\System\Config\Form::_initElement()
				 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L314-L320
				 */
				$b = df_new_om($backendC); /** @var B $b */
				$b->setPath($path);
				$b->setValue($value);
				/** 2016-08-03 By analogy with @see \Magento\Config\Block\System\Config\Form::getWebsiteCode() */
				$b->setWebsite(df_request('website', ''));
				/** 2016-08-03 By analogy with @see \Magento\Config\Block\System\Config\Form::getStoreCode() */
				$b->setStore(df_request('store', ''));
				$b->afterLoad();
				$config['value'] = $b->getValue();
			}
		}
		return [$elementId, $type, $config, $after, $isAdvanced];
	}
}
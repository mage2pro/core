<?php
namespace Df\Config\Table;
/**
 * 2015-04-17
 * Этот класс используется для хранения данных интерфейсного элемента
 * @see Df_Admin_Block_Field_DynamicTable
 * Пример использования смотрите в файле Df/1C/etc/system.xml модуля «1С: Управление торговлей»
 * для опции «Нестандартные символьные коды валют»:
		<non_standard_currency_codes>
			<label>Нестандартные символьные коды валют</label>
			<frontend_model>Df_1C_Config_Block_NonStandardCurrencyCodes</frontend_model>
			<backend_model>Df_Admin_Config_Backend_Table</backend_model>
			(...)
		</non_standard_currency_codes>
 *
 * Этот класс является более простой и удобной альтернативой
 * стандартному классу Magento CE @see Mage_Adminhtml_Model_System_Config_Backend_Serialized
 * @see Mage_Adminhtml_Model_System_Config_Backend_Serialized
 * проводит сериализацию и восстановление данных функциями @see serialize() и @see unserialize(),
 * что делает крайне неудобным ручной ввод данных.
 * Ручной ввод данных нужен, чтобы задавать для опций модулей значения по умолчанию
 * в секции «default» файла etc/config.xml модуля.
 *
 * Наш класс @see Df_Admin_Config_Backend_Table проводит сериализацию функцией @uses json_encode(),
 * а восстановление — одной из следующих функций:
 *
 * 1) @see json_decode() — если данные закодированы в формате JSON.
 * Значения по умолчанию задаются в слудующем формате:
	<default>
		<df_1c>
			<general>
				<non_standard_currency_codes>[["руб.", "RUB"],["грн", "UAH"]]</non_standard_currency_codes>
			</general>
		</df_1c>
	</default>
 * При этом имена колонок не указываются: сопоставление данных колонкам производится по порядку:
 * первое значение соответствует первой колонке таблицы и т.д.
 *
 * 2) @see df_csv_parse — если данные закодированы в формате CSV.
 * Используется для одноколоночных таблиц: например, списка стран.
 * Вы просто перечисляете значения через запятую, без обрамляющих кавычек и прочих элементов,
 * например:
	<default>
		<df_accounting>
			<vat>
				<eacu>AM,BY,RU,KG,KZ</eacu>
			</vat>
		</df_accounting>
	</default>
 *
 * 3) @unserialize() — для обратной совместимости с полями, котоыре раньше использовали
 * стандартный для Magento CE кодировщик
 * @see Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
 */
class Backend extends \Df\Config\Backend {
	/**
	 * 2015-04-17
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_afterLoad()
	 * @used-by \Magento\Framework\Model\AbstractModel::load()
	 * @used-by \Magento\Framework\App\Config\Value::afterLoad()
	 *
	 * 2015-12-07
	 * Не забывайте о дефекте https://mage2.pro/t/285
	 * «@see \Magento\Framework\App\Config\Value::afterLoad() method
	 * breaks specification of the overriden parent method
	 * @see \Magento\Framework\Model\AbstractModel::afterLoad()
	 * by not calling and ignoring its logic»
	 *
	 * Метод реализован по аналогии с
	 * @see \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized::_afterLoad()
	 *
	 * Обратите внимание, что в случае ручного задания данных,
	 * после распаковки данные будут в одним из следующих форматов:
	 * 1) array(array("руб.", "RUB"), array("грн", "UAH"))
	 * Этот формат используется для многоколоночной таблицы.
	 * 2) array("RU", "UA", "KZ")
	 * Этот формат используется для одноколоночной таблицы.
	 * Оба этих формата не являются стандартными для интерфейсного элемента управления
	 * и поэтому вручную преобразуется к стандартному.
	 * @return $this
	 */
	protected function _afterLoad() {
		/** @var mixed|Mage_Core_Model_Config_Element|string $value */
		$value = $this->getValue();
		if (!is_array($value)) {
			/**
			 * Почему-то сюда может приходить объект класса @see Mage_Core_Model_Config_Element
			 * следующего вида:
					Mage_Core_Model_Config_Element Object
					(
						[0] => [["руб.", "RUB"],["грн", "UAH"]]
					)
			 * Преобразуем его к строке.
			 */
			if ($value instanceof Mage_Core_Model_Config_Element) {
				$value = (string)$value;
			}
			/** @var string|bool $valueA */
			$valueA = false;
			if ($value && is_string($value)) {
				/**
				 * Узнаём класс строки.
				 * Класс строки описывается так:
					<eacu>
						<label>Страны ЕАЭС</label>
						<frontend_model>Df_Directory_Block_Field_CountriesOrdered</frontend_model>
						<backend_model>Df_Admin_Config_Backend_Table</backend_model>
						<rm_type>Df_Directory_Config_MapItem_Country</rm_type>
						(...)
					</eacu>
				 * @var string $rowClass
				 */
				$rowClass = $this->getFieldConfigParam('rm_type', $required = true);
				$valueA = self::unserialize($value, $rowClass);
			}
			$this->setValue($valueA ? $valueA : false);
		}
		return $this;
	}

	/**
	 * 2015-04-17
	 * Обратите внимание, что пакуем мы всегда в формате JSON.
	 * В CSV не пакуем даже одноколоночные таблицы, потому что нам это не нужно:
	 * запаковка используется для записи в БД, а вручную редактировать БД обычно не требуется.
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::_beforeSave()
	 * @used-by \Magento\Framework\Model\AbstractModel::save()
	 * Метод реализован по аналогии с
	 * @see \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized::_beforeSave()
	 * @return $this
	 */
	protected function _beforeSave() {
		/** @var mixed $value */
		$value = $this->getValue();
		if (is_array($value)) {
			unset($value['__empty']);
			/** @var string $valueS */
			$valueS = json_encode($value);
			$this->setValue($valueS);
		}
		return $this;
	}

	/**
	 * 2015-04-18
	 * @used-by \Df\Config\Table::_afterLoad()
	 * @used-by Df_Core_Model_Settings::map()
	 * @param string $value
	 * @param string $rowClass
	 * @return array(string => array(string => string))|null
	 */
	public static function unserialize($value, $rowClass) {
		/** @var array(string => array(string => string))|null $result */
		if (!df_contains($value, array('"', '[', '{'))) {
			/**
			 * Итак, мы имеем дело с простым форматом CSV, например:
				<default>
					<df_accounting>
						<vat>
							<eacu>AM,BY,RU,KG,KZ</eacu>
						</vat>
					</df_accounting>
				</default>
			 */
			$result = df_csv_parse($value);
		}
		else {
			/**
			 * Итак, мы имеем дело либо с форматом JSON,
			 * либо с форматом PHP @see serialize().
			 * Обратите внимание,
			 * что, несмотря на внешнюю похожесть формата PHP @see serialize() на JSON,
			 * данные в этом формате не являются валидным JSON.
			 * Например, для структуры array(array("руб.", "RUB"), array("грн", "UAH"))
			 * PHP @see serialize() вернёт строку:
			 * a:2:{i:0;a:2:{i:0;s:7:"руб.";i:1;s:3:"RUB";}i:1;a:2:{i:0;s:6:"грн";i:1;s:3:"UAH";}}
			 * Данные в формате JSON не могут начинаться с буквы («a»),
			 * а данные в формате PHP @see serialize() всегда начинаются с буквы.
			 * Поэтому мы сначала вызываем @uses json_decode().
			 * Если данные не являются JSON, то @uses json_decode() просто вернёт null,
			 * и тогда мы попробуем распаковать данные посредством PHP @uses unserialize()
			 *
			 * Кстати, возможно, что стоило бы просто проверять первую букву данных?
			 */
			/**
			 * Обязательно нужно указывать $assoc = true,
			 * чтобы после распаковки получить именно массив, а не объект
			 * (ведь до запаковки у нас был массив).
			 */
			$result = json_decode($value, $assoc = true);
		}
		if (is_array($result)) {
			df_assert(method_exists($rowClass, 'fields'));
			/**
			 * @uses Df_1C_Config_MapItem_CurrencyCode::fields()
			 * @uses Df_1C_Config_MapItem_PriceType::fields()
			 * @uses Df_Directory_Config_MapItem_Country::fields()
			 * @var string[] $fields
			 */
			$fields = call_user_func(array($rowClass, 'fields'));
			df_assert_array($fields);
			df_assert($fields);
			$result = self::normalize($result, $fields);
		}
		return $result;
	}

	/**
	 * 2015-04-17
	 * @used-by \Df\Config\Table::unserialize()
	 * @param mixed[] $result
	 * @param string[] $fields
	 * @return array(string => array(string => string))
	 */
	private static function normalize(array $result, array $fields) {
		/**
		 * Неассоциативность является самым простым критерием того,
		 * что у нас данные были распакованы из CSV или ручного JSON
		 * (потому что в ручном JSON названия колонок не указываются, например:
			<default>
				<df_1c>
					<general>
						<non_standard_currency_codes>[["руб.", "RUB"],["грн", "UAH"]]</non_standard_currency_codes>
					</general>
				</df_1c>
			</default>
		 * )
		 */
		if (array_key_exists(0, $result)) {
			/**
			 * Для начала преобразуем одномерный массив (формат CSV)
			 * к двумерному (ручной формат JSON):
			 * array("RU", "UA") => array(array("RU"), array("UA")))
			 */
			/** @uses df_array() */
			$result = array_map('df_array', $result);
			/**
			 * Теперь добавляем названия колонок:
			 * array(array("RU"), array("UA"))) =>
			 * array(array("code" => "RU"), array("code" => "UA")))
			 */
			$result = df_map('array_combine', $result, [], [$fields]);
			/**
			 * Теперь добавляем идентификаторы строк:
			 * array(array("code" => "RU"), array("code" => "UA"))) =>
				array(
					array("<идентификатор>" => array("code" => "RU"))
			 		,array("<идентификатор>" => array("code" => "UA"))
				)
			 *
			 * @see Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::getArrayRows()
			 * ожидает массив в следующем формате:
				Array
				(
					[_1429282691842_842] => Array
						(
							[non_standard_code] => руб.
							[standard_code] => RUB
						)

				)
			 * Здесь «_1429282691842_842» станет идентификатором строки таблицы
			 * (экранного элемента внутри DOM), а ключи второго уровня
			 * «non_standard_code» и «standard_code» — это идентификаторы колонок таблицы.
			 *
			 * Обратите внимание, что ключи верхнего уровня («_1429282691842_842»)
			 * никакой особой информации не несут, они просто должны быть уникальными.
			 * В @see app/design/adminhtml/default/default/template/system/config/form/field/array.phtml
			 * они формируются так: '_' + d.getTime() + '_' + d.getMilliseconds()
			 */
			/** @var string[] $rowIds */
			/** @uses df_uniqid() */
			$rowIds = array_map('df_uniqid', array_fill(0, count($result), 10));
			$result = array_combine($rowIds, $result);
		}
		return $result;
	}
}
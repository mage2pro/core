<?php
/**
 * 2017-04-04
 * Замечение №1.
 * Мы практически вынуждены вручную кодировать результат в JSON.
 * Мой анализ альтернатив:
 * 1.1) Возвращение АССОЦИАТИВНОГО МАССИВА.
 * Не работает, потому что ядро теряет ключи массива:
 * The @see \Magento\Framework\Webapi\ServiceOutputProcessor::convertValue() method
 * loses the keys of the associative arrays: https://mage2.pro/t/3601
 * 		$result[] = $datum;
 * https://github.com/magento/magento2/blob/2.1.5/lib/internal/Magento/Framework/Webapi/ServiceOutputProcessor.php#L102
 *
 * 1.2) Возвращение ОБЪЕКТА.
 * Не работает, потому что ядро пытается перевести объект в массив методом
 * @see \Magento\Framework\Reflection\DataObjectProcessor::buildOutputDataArray()
 * https://github.com/magento/magento2/blob/2.1.5/lib/internal/Magento/Framework/Reflection/DataObjectProcessor.php#L67-L126
 * Этот метод игнорирует все свойства объекта,
 * и в результирущий массив попадают только результаты вызовов публичных методов объекта.
 * ИТОГ:
 * Поэтому вот самая разумная возможность — вручную кодировать результат в JSON.
 * Ядро почти не трогает строки перед отправкой браузеру.
 *
 * Замечение №2.
 * Важно не кодировать здесь null в JSON.
 * Такое кодирование даст в результате строку 'null',
 * а затем ядро повторно будет кодировать эту строку в JSON в методе
 * @see \Magento\Framework\Webapi\Rest\Response\Renderer\Json::render()
 *
 * Замечение №3.
 * Счёл лучшим решением кодировать null в пустую строку,
 * потому что иначе ядро кодирует null в пустой массив:
 * @see \Magento\Framework\Webapi\ServiceOutputProcessor::convertValue()
 *		elseif ($data === null) {
 *			return [];
 *		}
 * https://github.com/magento/magento2/blob/2.1.5/lib/internal/Magento/Framework/Webapi/ServiceOutputProcessor.php#L109-L110
 * A Web API request returns an empty array for a null response: https://mage2.pro/t/1569
 * Такой пустой массив приводит к двусмысленности в браузере:
 * получается, что результат может быть либо объектом (если здесь $v является массивом или объектом),
 * либо строкой, либо пустым массивом.
 * Вот я и решил сократить в браузере количество вариаций с 3 до 2,
 * и поэтому стал кодировать null в пустую строку.
 *
 * Замечание №4.
 * int, float, bool пока намеренно не трогаю, но и не использую пока нигде.
 *
 * @used-by \Df\Payment\PlaceOrderInternal::p()
 * @param mixed $v
 * @return string|int|float|bool
 */
function dfw_encode($v) {return is_null($v) ? '' : (
	is_array($v) || is_object($v) ? df_json_encode($v) : $v
);}
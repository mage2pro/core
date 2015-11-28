<?php
/**
 * Обратите внимание, что класс Zend_Filter_StringTrim может работать некорректно
 * для строк, заканчивающихся заглавной кириллической буквой «Р».
 * http://framework.zend.com/issues/browse/ZF-11223
 * Однако решение, которое предложено по ссылке выше
 * (http://framework.zend.com/issues/browse/ZF-11223)
 * может приводить к падению интерпретатора PHP
 * для строк, начинающихся с заглавной кириллической буквы «Р».
 * Такое у меня происходило в методе @see Df_Autotrading_Model_Request_Locations::parseLocation()
 * Кто виноват: решение или исходный класс Zend_Filter_StringTrim — не знаю
 * (скорее, решение).
 * Поэтому мой класс \Df\Zf\Filter\StringTrim дополняет решение по ссылке выше
 * программным кодом из Zend Framework 2.0.
 */
namespace Df\Zf\Filter;
class StringTrim extends \Zend_Filter_StringTrim {
	/**
	 * @override
	 * @param string $value
	 * @param string $charlist
	 * @return string
	 */
	protected function _unicodeTrim($value, $charlist = '\\\\s') {
		if ('' === $value) {
			$result = $value;
		}
		else {
			// Начало кода из Zend Framework 2.0
			$chars = preg_replace(
				['/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'],
				['\\\\\\0', '\\', '\/'],
				$charlist
			);
  			$pattern = '/^[' . $chars . ']+|[' . $chars . ']+$/usSD';
			$result = preg_replace($pattern, '', $value);
			// Конец кода из Zend Framework 2.0
			if (null === $result) {
				/**
				 * Раньше тут происходил вызов @see df_notify_me().
				 * Заменил вызов @see df_notify_me() на @see df_notify_exception(),
				 * чтобы установить первоисточник странного поведения системы,
				 * когда в конец веб-адреса добавляется некий текстовый мусор в неверной кодировке
				 * (похожий на кусок диагностического сообщения),
				 * и затем этот веб-адрес попадает в данный метод,
				 * что приводит нас данную точку.
				 * Веб-адрес с мусором на конце может быть, например, таким:
				 * «/contacts/index/++++++++++++++++++++++++++++++++++++++++++Result:+�å+������ü+����û+��ÿ+�������è;»
				 * Неверность кодировки объясняется, видимо, функциями ядра для работы с веб-адресами.
				 */
				//df_notify_exception(
				//	df_sprintf('Не получается выполнить стандартный trim для строки: «%s».', $value)
				//);
				$result = $this->_slowUnicodeTrim($value, $charlist);
			}
		}
		return $result;
	}

	/**
	 * @param $value
	 * @param $chars
	 * @return string
	 */
	private function _slowUnicodeTrim($value, $chars) {
		$utfChars = $this->_splitUtf8($value);
		$pattern = '/^[' . $chars . ']$/usSD';
		while ($utfChars && preg_match($pattern, $utfChars[0])) {
			array_shift($utfChars);
		}
		while ($utfChars && preg_match($pattern, $utfChars[count($utfChars) - 1])) {
			array_pop($utfChars);
		}
		return implode($utfChars);
	}

	/**
	 * @param $value
	 * @return array|bool
	 */
	private function _splitUtf8($value) {
		try {
			$utfChars = str_split(iconv('UTF-8', 'UTF-32BE', $value), 4);
		}
		catch (\Exception $e) {
			df_error(
				'Система получила строку в кодировке, отличной от требуемой кодировки UTF-8: «%s».'
				,$value
			);
		}
		array_walk($utfChars, create_function('&$char', '$char = iconv("UTF-32BE", "UTF-8", $char);'));
		return $utfChars;
	}
}
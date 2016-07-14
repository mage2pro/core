<?php
use Magento\Framework\Phrase;

/**
 * 2015-08-15
 * @return string
 */
function df_locale() {
	/** @var string $result */
	static $result;
	if (!isset($result)) {
		/**
		 * 2015-10-22
		 * Отдельно обрабатываем случай запроса вида:
		 * http://localhost.com:900/store/pub/static/adminhtml/Magento/backend/ru_RU/js-translation.json
		 * Если при таком запросе использовать стандартную обработку, то почему-то слетает сессия.
		 */
		/** @var string $urlParts */
		$urlParts = explode('/', df_current_url());
		/** @var string $fileName */
		$fileName = array_pop($urlParts);
		/**
		 * 2015-10-22
		 * m_request_o()->isAjax() здесь не работает:
		 * RequireJS text plugin (lib/web/requirejs/text.js) does not set «X-Requested-With» HTTP header
		 * so the @see \Zend\Http\Request::isXmlHttpRequest() method returns a wrong value:
		 https://github.com/magento/magento2/issues/2159
		 */
		if ($fileName === \Magento\Translation\Model\Js\Config::DICTIONARY_FILE_NAME) {
			$result = array_pop($urlParts);
		}
		if (!isset($result)) {
			/** @var \Magento\Framework\Locale\Resolver $resolver */
			/**
			 * 2015-09-20
			 * Обратите внимание, что перечисленные ниже классы ведут себя по-разному.
			 * Класс \Magento\Backend\Model\Locale\Resolver просто вернёт локально по умолчанию,
			 * а класс \Magento\Framework\Locale\Resolver учитывает предпочтения администратора.
			 * @see \Magento\Backend\Model\Locale\Resolver::setLocale()
			 * Когда мы запрашиваем интерфейс — мы получаем нужный результат:
			 * \Magento\Backend\Model\Locale\Resolver или \Magento\Framework\Locale\Resolver
			 */
			$resolver = df_o(\Magento\Framework\Locale\ResolverInterface::class);
			$result = $resolver->getLocale();
		}
	}
	return $result;
}

/**
 * 2016-07-14
 * @param string|Phrase $text
 * @return Phrase
 */
function df_phrase($text) {return $text instanceof Phrase ? $text : __($text);}

/**
 * 2015-09-29
 * @used-by df_map_to_options_t()
 * @used-by \Df\Eav\Model\Entity\AttributePlugin::aroundGetStoreLabels()
 * @param string[] $strings
 * @param bool $now [optional]
 * @return string[]
 */
function df_translate_a($strings, $now = false) {
	/** @var string[] $result */
	$result = array_map('__', $strings);
	if ($now) {
		/**
		 * Иногда нужно перевести строки именно сейчас,
		 * чтобы не выпасть из контекста перевода.
		 * @see \Dfr\Translation\Realtime\Dictionary
		 */
		$result = array_map('strval', $result);
	}
	return $result;
}


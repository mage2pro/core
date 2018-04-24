<?php
use Df\Directory\Model\Country;
use Magento\Framework\Locale\Format;
use Magento\Framework\Locale\FormatInterface as IFormat;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Locale\ResolverInterface as IResolver;

/**               
 * 2017-09-03  
 * @used-by df_lang_ru()
 * @used-by df_lang_zh()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 * @param string|null $locale [optional]
 * @return string
 */
function df_lang($locale = null) {return substr(df_locale($locale), 0, 2);}

/**            
 * 2017-04-15 
 * @used-by df_lang_ru_en() 
 * @used-by \Df\Config\Source\EnableYN::toOptionArray()
 * @param mixed[] ...$args 
 * @return bool
 */
function df_lang_ru(...$args) {return df_b($args, 'ru' === df_lang());}

/**               
 * 2017-09-03    
 * @used-by \Dfe\Qiwi\API\Validator::codes()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfe\Robokassa\Charge::pCharge()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @return string
 */
function df_lang_ru_en() {return df_lang_ru('ru', 'en');}

/**
 * 2018-04-21
 * @used-by df_lang_zh_en()
 * @used-by \Doormall\Shipping\Partner\Entity::title()
 * @param mixed[] ...$args
 * @return bool
 */
function df_lang_zh(...$args) {return df_b($args, 'zh' === df_lang());}

/**
 * 2018-04-24
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @return string
 */
function df_lang_zh_en() {return df_lang_zh('zh', 'en');}

/**
 * 2015-08-15               
 * @used-by df_currency_name()  
 * @used-by df_intl_dic_path()
 * @used-by df_lang()
 * @used-by df_lang_ru()  
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToNameUc()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromNameToCode()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromNameToCodeUc()
 * @used-by \Df\Geo\Client::s()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfe\CurrencyFormat\O::postProcess()
 * @used-by \Dfr\Core\PhraseRenderer::render()
 * @used-by \Dfr\Core\Realtime\Dictionary\Term::translated()
 * @param string|null $l [optional]
 * @return string
 */
function df_locale($l = null) {
	/** @var string $result */
	if ($l) {
		$result = $l;
	}
	else {
		static $cached; /** @var string $cached */
		if (!isset($cached)) {
			// 2015-10-22
			// Отдельно обрабатываем случай запроса вида:
			// http://localhost.com:900/store/pub/static/adminhtml/Magento/backend/ru_RU/js-translation.json
			// Если при таком запросе использовать стандартную обработку, то почему-то слетает сессия.
			$urlParts = explode('/', df_current_url()); /** @var string[] $urlParts */
			$fileName = array_pop($urlParts); /** @var string $fileName */
			/**
			 * 2015-10-22
			 * df_request_o()->isAjax() здесь не работает:
			 * `RequireJS text plugin (lib/web/requirejs/text.js) does not set «X-Requested-With» HTTP header
			 * so the @see \Zend\Http\Request::isXmlHttpRequest() method returns a wrong value`:
			 * https://github.com/magento/magento2/issues/2159
			 */
			if ($fileName === \Magento\Translation\Model\Js\Config::DICTIONARY_FILE_NAME) {
				$cached = array_pop($urlParts);
			}
			if (!isset($cached)) {
				/**
				 * 2015-09-20
				 * Обратите внимание, что перечисленные ниже классы ведут себя по-разному.
				 * Класс \Magento\Backend\Model\Locale\Resolver просто вернёт локально по умолчанию,
				 * а класс \Magento\Framework\Locale\Resolver учитывает предпочтения администратора.
				 * @see \Magento\Backend\Model\Locale\Resolver::setLocale()
				 * Когда мы запрашиваем интерфейс — мы получаем нужный результат:
				 * \Magento\Backend\Model\Locale\Resolver или \Magento\Framework\Locale\Resolver
				 */
				$resolver = df_o(IResolver::class); /** @var IResolver|Resolver $resolver */
				$cached = $resolver->getLocale();
			}
		}
		$result = $cached;
	}
	return $result;
}

/**
 * 2017-01-29
 * «US» => «en_US», «SE» => «sv_SE».
 * Some contries has multiple locales (e.g., Finland has the «fi_FI» and «sv_FI» locales).
 * The function returns the default locale: «FI» => «fi_FI».
 * @used-by df_currency_by_country_c()
 * @used-by \Df\Payment\Charge::locale()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge::locale()
 * @param string|Country $c
 * @return string
 */
function df_locale_by_country($c) {return \Zend_Locale::getLocaleToTerritory(df_country_code($c));}

/**
 * 2016-09-06
 * @return IFormat|Format
 */
function df_locale_f() {return df_o(IFormat::class);}
<?php
use Df\Core\Exception as DFE;

/**
 * Этот метод предназначен для извлечения некоторого значения
 * из многомерного массива посредством нотации ключ1/ключ2/ключ3
 * Например: dfa_deep(array('test' => array('eee' => 3)), 'test/eee') вернёт «3».
 * Обратите внимание, что ядро Magento реализует аналогичный алгоритм
 * в методе @see \Magento\Framework\DataObject::getData()
 * Наша функция работает не только с объектами @see \Magento\Framework\DataObject, но и с любыми массивами.
 * 2017-03-28
 * Сегодня заметил, что успешно работают пути типа 'transactions/0'
 * в том случае, когда ключ верхнего уровня возвращает массив с целочисленными индексами.
 * @used-by df_fe_m()
 * @used-by df_primary_key()
 * @used-by dfa()
 * @used-by \Df\API\Client::resPath()
 * @used-by \Df\Config\Backend::value()
 * @used-by \Df\Config\Fieldset::_getHeaderCommentHtml()
 * @used-by \Df\Config\Plugin\Model\Config\Structure\Element\Iterator\Tab::beforeSetElements()
 * @used-by \Df\Core\O::offsetExists()
 * @used-by \Df\Core\O::offsetGet()
 * @used-by \Df\GingerPaymentsBase\Block\Info::psDetails()
 * @used-by \Df\Payment\ConfigProvider::p()
 * @used-by \Df\StripeClone\Block\Info::cardDataFromChargeResponse()
 * @used-by \Dfe\AllPay\W\Event::tlByCode()
 * @used-by \Dfe\AlphaCommerceHub\API\Validator::result()
 * @used-by \Dfe\CheckoutCom\Handler::r()
 * @used-by \Dfe\Dynamics365\API\Validator\JSON::short()
 * @used-by \Dfe\FacebookLogin\Customer::picture()
 * @used-by \Dfe\Moip\API\Validator::short()
 * @used-by \Dfe\Stripe\Facade\Card::__construct()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @used-by \Dfe\YandexKassa\Source\Option::map()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \TemplateMonster\Megamenu\Observer\PrepareCategory::execute()  (frugue.com)
 * @param array(string => mixed) $a
 * @param string|string[]|null $path
 * @param mixed $d [optional]
 * @return mixed|null
 * @throws DFE
 */
function dfa_deep(array $a, $path, $d = null) {/** @var mixed|null $r */ /** @var string[] $pathParts */
	if (df_nes($path)) {
		$r = $a;
	}
	elseif (is_array($path)) {
		$pathParts = $path;
	}
	else {
		df_param_sne($path, 1);
		if (isset($a[$path])) {
			$r = $a[$path];
		}
		else {
			/**
			 * 2015-02-06
			 * Обратите внимание, что если разделитель отсутствует в строке,
			 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
			 * Это вполне укладывается в наш универсальный алгоритм.
			 */
			$pathParts = df_explode_xpath($path);
		}
	}
	if (!isset($r)) {
		$r = null;
		while ($pathParts) {
			$r = dfa($a, array_shift($pathParts));
			if (is_array($r)) {
				$a = $r;
			}
			else {
				if ($pathParts) {
					$r = null; # Ещё не прошли весь путь, а уже наткнулись на не-массив.
				}
				break;
			}
		}
	}
	return is_null($r) ? $d : $r;
}

/**
 * 2015-12-07
 * @used-by \Df\Core\O::offsetSet()
 * @used-by \KingPalm\Core\Plugin\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\Grouped::afterModifyMeta()
 * @param array(string => mixed) $array
 * @param string|string[] $path
 * @param mixed $value
 * @return array(string => mixed)
 * @throws DFE
 */
function dfa_deep_set(array &$array, $path, $value):array {
	$pathParts = df_explode_xpath($path); /** @var string[] $pathParts */
	$a = &$array; /** @var array(string => mixed) $a */
	while ($pathParts) {
		$key = array_shift($pathParts); /** @var string $key */
		if (!isset($a[$key])) {
			$a[$key] = [];
		}
		$a = &$a[$key];
		if (!is_array($a)) {
			$a = [];
		}
	}
	$a = $value;
	return $array;
}

/**
 * 2018-04-24
 * 2018-04-25 @deprecated It is unused.
 * https://github.com/mage2pro/core/blob/3.7.13/Core/etc/doc/dfa_deep_slice/1.png
 * https://github.com/mage2pro/core/blob/3.7.13/Core/etc/doc/dfa_deep_slice/2.png
 * https://github.com/mage2pro/core/blob/3.7.13/Core/etc/doc/dfa_deep_slice/3.png
 */
function dfa_deep_slice(array $a, int $l):array {return 1 === $l ? (array_is_list($a) ? $a : array_keys($a)) : array_map(
	function($v) use($l) {return !is_array($v) ? $v : dfa_deep_slice($v, $l - 1);}, $a
);}

/**
 * 2017-07-13
 * @see dfa_unset()
 * @used-by dfa_deep_unset()
 * @used-by \Df\Core\O::offsetUnset()
 * @param array(string => mixed) $a
 * @param string|string[] $path
 * @throws DFE
 */
function dfa_deep_unset(array &$a, $path) {
	if (!is_array($path)) {
		df_param_sne($path, 1);
		/**
		 * 2015-02-06
		 * Обратите внимание, что если разделитель отсутствует в строке,
		 * то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
		 * Это вполне укладывается в наш универсальный алгоритм.
		 */
		$path = df_explode_xpath($path);
	}
	/**
	 * 2017-07-13
	 * @uses array_shift не выдаёт предупреждений для пустого массива.
	 * @var string|null $first
	 */
	if ($first = array_shift($path)) {
		if (!$path) {
			unset($a[$first]);
		}
		else {
			dfa_deep_unset($a[$first], $path);
		}
	}
}
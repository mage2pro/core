<?php
namespace Df\Directory\FE;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
// 2016-09-03
/** @see \Dfe\Stripe\FE\Currency */
class Currency extends Dropdown {
	/**
	 * 2016-09-04
	 * @override
	 * @see \Df\Framework\Form\Element\Select::getValue()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @see \Dfe\Stripe\FE\Currency::getValue()
	 * @return string|null
	 */
	function getValue() {
		$chosen = parent::getValue(); /** @var string|null $chosen */
		$allowed = $this->dfValues(); /** @var string[] $allowed */
		// 2016-11-13
		// Обрабатываем тот случай, когда значения self::$ORDER и self::$BASE были разрешены
		// в предыдущих версиях модуля, а потом стали запрещены.
		// Так, например, было с модулем Square:
		// там на сегодняшний день разрешены всего 2 валюты: USD и CAD,
		// поэтому я решил убрать опции self::$ORDER и self::$BASE,
		// однако чтобы это не поломало магазины тех клиентов,
		// у которых одно из этих значений уже выбрано (а self::$ORDER было значением по умолчанию).
		return $allowed && (!$chosen || !in_array($chosen, $allowed)) ? df_first($allowed) :
			($chosen ?: self::$ORDER)
		;
	}

	/**
	 * 2016-09-04
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::getValues()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * Делаем по аналогии с @see \Magento\Config\Model\Config\Structure\Element\Field::getOptions()
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L362-L379
	 *
	 * Наша реализация короче, чем здесь:
	 * @see \Magento\Config\Model\Config\Structure\Element\Field::_getOptionsFromSourceModel()
	 * https://github.com/magento/magento2/blob/2.1.1/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L421-L456
	 *
	 * И лучше, потому что @uses df_currencies_options() не вываливает список всех валют, как в ядре,
	 * а показывает только доступные для данной витрины валюты.
	 * Более того, проверяет, имеется ли в системе курс обмена данной валюты на учётную.
	 *
	 * Подумав, заметил, что вопрос-то тонкий...
	 * Мы можем находиться, например, на области действия настроек website или default,
	 * в то время как на области store настройки валют могут быть разными...
	 * Вызов @uses df_currencies_options() без параметров использует именно настройки по-умолчанию.
	 * Это неидеальное решение.
	 * Пока клиенты не жалуются (и неочевидно, будут ли вообще жаловаться) — пусть будет так.
	 * А если надо будет, то текущий scope можено получить так:
	 * df_scope($this['scope_id'], $this['scope']);
	 *
	 * 2016-12-15
	 * Функцию df_scope удалил, потому что в Magento CE 2.1.3 из ядра был удалён класс
	 * \Magento\Framework\App\Config\ScopePool : https://github.com/magento/magento2/commit/3660d012
	 * Отныне получить значение опции в конкретном scope можно посредством @see df_cfg(),
	 * передав в качестве $scope массив из 2-х элементов: [Scope Type, Scope Code]
	 *
	 * @return array(array(string => string))
	 */
	function getValues() {return dfc($this, function() {$v = $this->dfValues(); return
		// 2016-12-26 Здесь нужно именно array_merge(), потому что индексы — целочисленные.
		$v ? df_currencies_options($v) : array_merge(
			df_map_to_options_t([self::$ORDER => 'Order Currency', self::$BASE => 'Base Currency'])
			,df_currencies_options()
		)
	;});}

	/**
	 * 2016-09-05
	 * Текущая валюта может меняться динамически (в том числе посетителем магазина и сессией),
	 * поэтому мы используем параметр $store, а не $scope
	 * @used-by \Df\Payment\Currency::iso3()
	 * @param string $code
	 * @param null|string|int|S|Store $store [optional]
	 * @param string|null $orderCurrency [optional]
	 * @return string
	 */
	final static function v($code, $store = null, $orderCurrency = null) {return dftr(
		$code ?: self::$ORDER, self::map($store, $orderCurrency)
	);}

	/**
	 * 2016-09-05
	 * @used-by v()
	 * @param null|string|int|S|Store $store [optional]
	 * @param string|null $orderCurrency [optional]
	 * @return array(string => string|null)
	 */
	private static function map($store = null, $orderCurrency = null) {return dfcf(
		function($store = null, $orderCurrency = null) {return [
			self::$BASE => df_currency_base_c($store)
			,self::$ORDER => $orderCurrency ?: df_currency_current_c($store)
		];}
	, func_get_args());}

	/**
	 * 2016-09-05
	 * @var string
	 */
	private static $BASE = 'base';
	/**
	 * 2016-09-05
	 * @var string
	 */
	private static $ORDER = 'order';
}
<?php
// 2016-09-03
namespace Df\Payment\FormElement;
use Magento\Config\Model\Config\Source\Locale\Currency as Source;
use Magento\Directory\Model\Currency as CurrencyM;
use Magento\Framework\App\ScopeInterface as S;
use Magento\Store\Model\Store;
class Currency extends \Df\Framework\Form\Element\Select2 {
	/**
	 * 2016-09-04
	 * @override
	 * @see \Df\Framework\Form\Element\Select::getValue()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return string|null
	 */
	public function getValue() {
		/** @var string|null $result */
		$result = parent::getValue();
		/** @var string[] $filter */
		$filter = $this->filter();
		return
			/**
			 * 2016-11-13
			 * Обрабатываем тот случай, когда значения self::$ORDER и self::$BASE были разрешены
			 * в предыдущих версиях модуля, а потом стали запрещены.
			 * Так, например, было с модулем Square:
			 * там на сегодняшний день разрешены всего 2 валюты: USD и CAD,
			 * поэтому я решил убрать опции self::$ORDER и self::$BASE,
			 * однако чтобы это не поломало магазины тех клиентов,
			 * у которых одно из этих значений уже выбрано (а self::$ORDER было значением по умолчанию).
			 */
			$filter && (!$result || !in_array($result, $filter))
			? df_first($filter)
			: ($result ?: self::$ORDER)
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
	public function getValues() {return dfc($this, function() {return
		$this->filter() ? df_currencies_options($this->filter()) :
			df_map_to_options_t([
				self::$ORDER => 'Order Currency', self::$BASE => 'Base Currency'
			]) + df_currencies_options()
	;});}

	/**
	 * 2016-09-03
	 * @override
	 * @see \Df\Framework\Form\Element\Select2::onFormInitialized()
	 * @return void
	 */
	public function onFormInitialized() {
		parent::onFormInitialized();
		df_fe_init($this, __CLASS__);
	}

	/**
	 * 2016-09-05
	 * Текущая валюта может меняться динамически (в том числе посетителем магазина и сессией),
	 * поэтому мы используем параметр $store, а не $scope
	 * @param string $code
	 * @param null|string|int|S|Store $store [optional]
	 * @param CurrencyM|string|null $orderCurrency [optional]
	 * @return CurrencyM
	 */
	public static function v($code, $store = null, $orderCurrency = null) {return
		df_currency(dftr($code ?: self::$ORDER, self::map($store, $orderCurrency)))
	;}

	/**
	 * @override
	 * 2016-09-03
	 * Этот стиль присваивается:
	 * 1) Выпадающему списку select2.
	 * 2) Оригинальному элементу select (который при использовании select2 вроде бы роли не играет).
	 * 3) Родительскому контейнеру .df-field, который присутствует в том случае,
	 * если наш элемент управления был создан внутри нашего нестандартного филдсета,
	 * и осутствует, если наш элемент управления является элементом управления вернхнего уровня
	 * (то есть, указан в атрибуте «type» тега <field>).
	 * @see \Df\Framework\Form\Element\Select2::customCssClass()
	 * @used-by \Df\Framework\Form\Element\Select2::setRenderer()
	 * @return string
	 */
	protected function customCssClass() {return 'df-payment-currency';}

	/**
	 * 2016-11-13
	 * Поддержка фиксированного списка валют.
	 * Используется модулем «Omise»:
	 * https://code.dmitry-fedyuk.com/m2e/omise/blob/0.0.6/etc/adminhtml/system.xml#L154
	 * При таком синтаксисе мы намеренно не добавляем в результат «Order Currency» и «Base Currency».
	 * Метод будет возвращать только те значения из dfValues,
	 * которые включены администратором в перечень разрешённых валют.
	 * @return string[]
	 */
	private function filter() {return dfc($this, function() {return df_fe_fc_csv($this, 'dfValues');});}

	/**
	 * 2016-09-05
	 * @used-by \Df\Payment\FormElement\Currency::v()
	 * @param null|string|int|S|Store $store [optional]
	 * @param CurrencyM|string|null $orderCurrency [optional]
	 * @return array(string => CurrencyM|string|null)
	 */
	private static function map($store = null, $orderCurrency = null) {return dfcf(
		function($store = null, $orderCurrency = null) {return [
			self::$BASE => df_currency_base($store)
			,self::$ORDER => $orderCurrency ?: df_currency_current($store)
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
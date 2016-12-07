<?php
namespace Df\Sales\Setup;
use Magento\Quote\Setup\QuoteSetup;
use Magento\Sales\Setup\SalesSetup;
/**
 * 2016-08-14
 * ЭТОТ КЛАСС ПОКА НИКАК НЕ ИСПОЛЬЗУЕТСЯ (и он абстрактный).
 * Пример использования: $this->addFieldToSalesItems('df');
 *
 * Метод addFieldToSalesItems добавляет к таблицам
 * quote_item, order_item, invoice_item, creditmemo_item новое поле (в примере: «df»):
 * это позволяет нам учитывать для соответствующих объектов
 * дополнительную информацию с неизвестной заранее структурой.
 * Информация будет храниться в сериализованном виде.
 *
 * В ядре аналогичное по роли поле additional_information (а также, видимо, additional_data)
 * присутствует у таблиц
 *
 * Пока (2016-08-14) эти поля никак не используются:
 * после разработки класса понял, текущую задачу надо делать иначе
 * (как раз используя упомянутое выше поле additional_information
 * таблиц quote_payment и sales_order_payment).
 * Но поле «df» тоже очень полезно, и я оставил его на будущее.
 */
abstract class Schema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2016-08-14
	 * @param string $fieldName
	 * @return void
	 */
	protected function addFieldToSalesItems($fieldName) {
		/** @var array(string => string) $params */
		$params = ['type' => 'text'];
		$this->sQuote()->addAttribute('quote_item', $fieldName, $params);
		/** @uses \Magento\Sales\Setup\SalesSetup::addAttribute() */
		df_map([$this->sSales(), 'addAttribute'],
			['creditmemo_item', 'invoice_item', 'order_item'], [$fieldName, $params]
		);
	}

	/**
	 * 2016-08-14
	 * @return QuoteSetup
	 */
	private function sQuote() {return $this->sEav(QuoteSetup::class);}

	/**
	 * 2016-08-14
	 * @return SalesSetup
	 */
	private function sSales() {return $this->sEav(SalesSetup::class);}
}
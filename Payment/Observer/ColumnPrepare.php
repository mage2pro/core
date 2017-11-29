<?php
namespace Df\Payment\Observer;
use Df\Ui\Plugin\Component\Listing\Columns\Column as Plugin;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Ui\Component\Listing\Columns\Column;
/**
 * 2016-07-28
 * Событие: df_column__prepare
 * @see \Df\Ui\Plugin\Component\Listing\Columns\Column::beforePrepare()
 * Цель обработчика — задействование в качестве результата сырого значения
 * в том случае, когда сырое значение отсутствует в справочнике значений.
 * Родительская реалиация работает так: если сырое значение отсутствует в справочнике значений,
 * то родительская реализация возвращает пустую строку.
 *
 * Нас это не устраивает.
 * Мы хотим возможности отображения в колонке «Payment Method»
 * административной таблице заказов расширенного названия способа оплаты для заказов.
 * Эти расширенные названия будут настраиваться моими конкретными платёжными модулями.
 * Например, вместо «歐付寶 O'Pay (allPay)» может отображаться «歐付寶 O'Pay (allPay) (Bank Card)».
 *
 * В ядре в данном контексте сырым значением является код способа оплаты,
 * например: «dfe_allpay».
 * Далее ядро смотрит в справочнике, какое название соответствует коду «dfe_allpay»,
 * и возвращает строку «歐付寶 O'Pay (allPay)».
 *
 * В нашем же случае мы методом \Df\Payment\Observer\DataProvider\SearchResult::execute()
 * запихнули в поле «payment_method» наших строк расширенное название наших способов оплаты
 * (например, «歐付寶 O'Pay (allPay) (Bank Card)»).
 *
 * Разумеется, такие значения отсутствуют в справочнике значений.
 * Вот мы и хотим, чтобы в такой ситуации возвращалась не пустая строка, а сырое значение.
 */
final class ColumnPrepare implements ObserverInterface {
	/**
	 * 2016-07-28
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {
		/** @var Column $c */
		$c = $o[Plugin::COLUMN];
		if ('payment_method' === $c->getName()
			&& 'sales_order_grid' === $c->getContext()->getNamespace()
		) {
			$c['config'] = [
				'bodyTmpl' => 'ui/grid/cells/html'
			   ,'component' => 'Df_Ui/js/grid/columns/select'
		   ] + $c['config'];
		}
	}
}


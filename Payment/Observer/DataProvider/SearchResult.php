<?php
namespace Df\Payment\Observer\DataProvider;
use Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider as Plugin;
use Magento\Framework\Api\Search\SearchResult as ApiSearchResult;
use Magento\Framework\Api\Search\SearchResultInterface as ISearchResult;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as Provider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as UiSearchResult;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Grid\Collection as CreditmemoGC;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGC;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection as InvoiceGC;
/**
 * 2016-07-28
 * Событие: df_data_provider__search_result
 * @see \Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider::afterGetSearchResult()
 * Цель обработчика — реализация возможности отображения в колонке «Payment Method»
 * административной таблице заказов расширенного названия способа оплаты для заказов.
 * Эти расширенные названия будут настраиваться моими конкретными платёжными модулями.
 * Например, вместо «歐付寶 allPay» может отображаться «歐付寶 allPay (Bank Card)».
 */
final class SearchResult implements ObserverInterface {
	/**
	 * 2016-07-28
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param O $o
	 */
	function execute(O $o) {
		/** @var Provider $provider */
		$provider = $o[Plugin::PROVIDER];
		/** @var ISearchResult|ApiSearchResult|UiSearchResult|OrderGC|InvoiceGC|CreditmemoGC $result */
		$result = $o[Plugin::RESULT];
		if (in_array($provider->getName(), [
			'sales_order_grid_data_source'
			/*,'sales_order_invoice_grid_data_source'
			,'sales_order_creditmemo_grid_data_source' */
		])) {
			/**
			 * 2016-07-28
			 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/UiComponent/DataProvider/SearchResult.php#L37-L40
			 * @see \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult::$document
			 * Структура документа описана здесь: https://mage2.pro/t/1908
			 */
			/** @var string $cacheKey */
			$cacheKey = __METHOD__;
			/** @var string $prop */
			$prop = 'payment_method';
			df_map(function(Document $item) use($cacheKey, $prop) {
				/** @var string|null $methodCode */
				if (($methodCode = $item[$prop]) && df_starts_with($methodCode, 'dfe_')) {
					/** @var int $id */
					$id = $item['entity_id'];
					// 2016-07-29
					// Эта операция очень ресурсоёмка:
					// для каждой строки таблицы заказов она делает кучу запросов к базе данных.
					// Поэтому кэшируем результаты в постоянном кэше.
					$item[$prop] = df_cache_get_simple([$cacheKey, $id], function() use ($id) {return
						dfpm(df_order($id))->titleDetailed()
					;});
				}
			}, $result);
		}
	}
}
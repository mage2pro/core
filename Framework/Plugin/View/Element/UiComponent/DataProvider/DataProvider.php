<?php
namespace Df\Framework\Plugin\View\Element\UiComponent\DataProvider;
use Magento\Framework\Api\Search\SearchResult as ApiSearchResult;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as Sb;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as UiSearchResult;
class DataProvider {
	/**
	 * 2016-07-28
	 * Цель плагина — предоставление программистам удобной возможности
	 * модификации коллекции $result, которая служит источником данных для таблиц.
	 *
	 * @see \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider::getSearchResult()
	 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/UiComponent/DataProvider/DataProvider.php#L308-L316
	 *
	 * Статьи по теме:
	 * «How are the rows data retrieved for the backend's orders grid?» https://mage2.pro/t/1907
	 * «How is @see \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider::searchResultToOutput() implemented?»
	 * https://mage2.pro/t/1908
	 * «How is the «Payment Method» column implemented for the backend's orders grid?»
	 * https://mage2.pro/t/1906
	 *
	 * Обратите внимание, что мы могли вместо плагина
	 * перекрыть класс конкретной коллекции, например:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/etc/di.xml#L768
	 *	<type name='Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory'>
	 *		<arguments>
	 *			<argument name='collections' xsi:type='array'>
	 *				<item name='sales_order_grid_data_source' xsi:type='string'
	 *				>Df\Sales\Model\ResourceModel\Order\Grid\Collection</item>
	 *			</argument>
	 *		</arguments>
	 *	</type>
	 * Однако у такого подхода 2 недостатка:
	 * 1) он неуниверсален и каждую конкретную коллекцию надо перекрывать отдельно.
	 * 2) он хрупок: другие программисты могут захотеть перекрыть ту же коллекцию.
	 *
	 * Наш подход намного лучше.
	 *
	 * @param Sb $sb
	 * @param SearchResultInterface|ApiSearchResult|UiSearchResult $result
	 * @return string
	 */
	function afterGetSearchResult(Sb $sb, SearchResultInterface $result) {
		df_dispatch('df_data_provider__search_result', [self::PROVIDER => $sb, self::RESULT => $result]);
		return $result;
	}

	/**
	 * 2016-07-28
	 * @used-by \Df\Payment\Observer\DataProvider\SearchResult::execute()
	 * @used-by \Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider::afterGetSearchResult()
	 */
	const PROVIDER = 'provider';
	const RESULT = 'result';
}
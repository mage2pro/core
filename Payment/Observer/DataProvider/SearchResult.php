<?php
namespace Df\Payment\Observer\DataProvider;
use Df\Framework\Plugin\View\Element\UiComponent\DataProvider\DataProvider as Plugin;
use Df\Payment\Method as M;
use Magento\Framework\Api\Search\SearchResult as ApiSearchResult;
use Magento\Framework\Api\Search\SearchResultInterface as ISearchResult;
use Magento\Framework\Event\Observer as Ob;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as Provider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as UiSearchResult;
use Magento\Payment\Model\Method\Substitution;
use Magento\Sales\Model\Order as O;
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
 * Например, вместо «歐付寶 O'Pay (allPay)» может отображаться «歐付寶 O'Pay (allPay) (Bank Card)».
 */
final class SearchResult implements ObserverInterface {
	/**
	 * 2016-07-28
	 * @override
	 * @see ObserverInterface::execute()
	 * @used-by \Magento\Framework\Event\Invoker\InvokerDefault::_callObserverMethod()
	 * @param Ob $o
	 */
	function execute(Ob $o) {
		$provider = $o[Plugin::PROVIDER]; /** @var Provider $provider */
		/** @var ISearchResult|ApiSearchResult|UiSearchResult|OrderGC|InvoiceGC|CreditmemoGC $result */
		$result = $o[Plugin::RESULT];
		// 2017-08-02 For now, we do not handle «sales_order_invoice_grid_data_source»
		// and «sales_order_creditmemo_grid_data_source».
		if ('sales_order_grid_data_source' === $provider->getName()) {
			/**
			 * 2016-07-28
			 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/View/Element/UiComponent/DataProvider/SearchResult.php#L37-L40
			 * @see \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult::$document
			 * Структура документа описана здесь: https://mage2.pro/t/1908
			 */
			$cacheKey = __METHOD__; /** @var string $cacheKey */
			$prop = 'payment_method';  /** @var string $prop */
			df_map(function(Document $item) use($cacheKey, $prop) {
				/** @var string|null $methodCode */
				if (($methodCode = $item[$prop]) && df_starts_with($methodCode, 'dfe_')) {
					/**
					 * 2017-05-09
					 * Как оказалось, наличие приставки «dfe_» ешё не гарантирует,
					 * что @uses dfpm() (вызываемый ниже опосредованно через dfpm_title())
					 * действительно вернёт наследника @see \Df\Payment\Method
					 * Возможна ситуация, когда админтратор установил какой-либо из моих патёжных модулей,
					 * сделал платёж посредством него, а затем либо удалил модуль,
					 * либо из-за каких-то некорректных действий модуль стал отключенным,
					 * и вот тогда M2 вернёт не наследника @see \Df\Payment\Method,
					 * а экземпляр класса @see \Magento\Payment\Model\Method\Substitution:
					 * «How is @see \Magento\Payment\Model\Method\Substitution implemented and used?»
					 * https://mage2.pro/t/3924
					 * 1) @see \Magento\Sales\Model\Order\Payment\Info::getMethodInstance()
					 *	try {
					 *		$instance = $this->paymentData->getMethodInstance($this->getMethod());
					 *	} catch (\UnexpectedValueException $e) {
					 *		$instance = $this->paymentData->getMethodInstance(Substitution::CODE);
					 *	}
					 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Sales/Model/Order/Payment/Info.php#L113-L117
					 * 2) @see \Magento\Payment\Model\Info::getMethodInstance()
					 *	try {
					 *		$instance = $this->_paymentData->getMethodInstance($this->getMethod());
					 *	} catch (\UnexpectedValueException $e) {
					 *		$instance = $this->_paymentData->getMethodInstance(Method\Substitution::CODE);
					 *	}
					 * https://github.com/magento/magento2/blob/2.1.6/app/code/Magento/Payment/Model/Info.php#L106-L110
					 */
					/** @var int $id */
					$id = $item['entity_id'];
					// 2016-07-29
					// Эта операция очень ресурсоёмка:
					// для каждой строки таблицы заказов она делает кучу запросов к базе данных.
					// Поэтому кэшируем результаты в постоянном кэше.
					$item[$prop] = df_cache_get_simple([$cacheKey, $id], function() use($id) {
						$m = dfpm($o = df_order($id)); /** @var O $o */ /** @var M|Substitution $m */
						return !dfp_my($m) ? $m->getTitle() : df_cc_br(dfpm_title($m), dfp_choice($o)->title());
					});
				}
			}, $result);
		}
	}
}
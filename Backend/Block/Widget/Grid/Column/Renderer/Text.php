<?php
namespace Df\Backend\Block\Widget\Grid\Column\Renderer;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
/**
 * 2016-08-19
 * 2017-01-13
 * Этот класс используется в сценарии отображения информации о платёжной транзакции
 * в административном интерфейсе Magento:
 * https://site.com/admin/sales/transactions/view/txn_id/347/order_id/354/
 * @see \Magento\Backend\Block\Widget\Grid\Column::getRenderer()
 * @used-by \Df\Sales\Plugin\Block\Adminhtml\Transactions\Detail\Grid
 */
class Text extends AbstractRenderer {
	/**
	 * 2016-08-19
	 * 2017-01-13
	 * $row — это элемент коллекции с параметрами платёжной транзакции.
	 * Каждому такому элементу будет соответствовать строка таблицы.
	 * Элемент содержит 2 свойства: «key» и «value».
	 * Эти свойства будут колонками таблицы.
	 * Колекция наполняется в методе
	 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::_prepareCollection()
			foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
				$data = new \Magento\Framework\DataObject(['key' => $key, 'value' => $value]);
				$collection->addItem($data);
			}
	 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L68-L71
	 *
	 * Мои платёжные модули записывают информацию о своих транзакциях в методах:
	 * 1) @see \Df\StripeClone\Method::transInfo()
	 * https://github.com/mage2pro/core/blob/1.11.11/StripeClone/Method.php?ts=4#L302
	 * 2) @see \Df\PaypalClone\Method::getConfigPaymentAction()
	 * https://github.com/mage2pro/core/blob/1.11.11/PaypalClone/Method.php#L58-L60
	 * 3) @see \Df\Payment\W\Handler::addTransaction()
	 * https://github.com/mage2pro/core/blob/1.11.11/Payment/Webhook.php?ts=4#L150
	 *
	 * @override
	 * @see \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer::render()
	 * @used-by \Magento\Backend\Block\Widget\Grid\Column::getRowField()
	 * @used-by \Df\Sales\Plugin\Block\Adminhtml\Transactions\Detail\Grid::beforeAddColumn()
	 * @param DataObject $row
	 * @return string
	 */
	function render(DataObject $row) {
		/** @var string|array(string => mixed)|null $v */
		$v = $this->_getValue($row);
		// 2017-01-13
		// Раньше я выполнял такое кодирование при записи информации в транзакцию.
		// Теперь я это стал делать непосредственно перед отображением: так надёжнее,
		// потому что ранее я порой ненароком забывал сконвертировать какой-нибудь массив в JSON
		// перед записью, и при отображении это приводило к сбою «array to string conversion».
		if (is_array($v)) {
			$v = df_json_encode($v);
		}
		elseif (is_bool($v)) {
			$v = df_bts($v);
		}
		/** @var string $result */
		$result = df_check_json_complex($v) || df_check_xml($v) ? df_tag('pre', [], $v) : $v;
		/**
		 * 2017-01-13
		 * Результат должен быть строкой,
		 * потому что он используется в шаблоне *.phtml с оператором echo:
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backend/view/adminhtml/templates/widget/grid/extended.phtml#L194
		 */
		return df_result_s($result);
	}
}


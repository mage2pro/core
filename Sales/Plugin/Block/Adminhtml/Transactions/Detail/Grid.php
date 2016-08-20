<?php
namespace Df\Sales\Plugin\Block\Adminhtml\Transactions\Detail;
use Df\Backend\Block\Widget\Grid\Column\Renderer\Text;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid as Sb;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
// 2016-08-19
class Grid {
	/**
	 * 2016-08-19
	 * Цель плагина — форматирование значений таблицы параметров платёжной транзакции, чтобы:
	 * 1) в них сохранялись переносы строк,
	 * 2) длиные значения не обрезались после 250 символов.
	 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::addColumn()
	 * @used-by \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::_prepareColumns()
	 * @param Sb $sb
	 * @param array(string => array(string => string)) $elements
	 * @param string $columnId
	 * @param array $column
	 * @return array
	 */
	public function beforeAddColumn(Sb $sb, $columnId, array $column) {
		/** @var bool $apply */
		$apply = 'value' === $columnId && df_trans_is_my();
		return [$columnId, ($apply ? ['renderer' => Text::class] : []) + $column];
	}
}
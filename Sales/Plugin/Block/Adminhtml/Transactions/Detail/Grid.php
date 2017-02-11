<?php
namespace Df\Sales\Plugin\Block\Adminhtml\Transactions\Detail;
use Df\Backend\Block\Widget\Grid\Column\Renderer\Text;
use Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid as Sb;
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
	function beforeAddColumn(Sb $sb, $columnId, array $column) {
		/**
		 * 2017-01-13
		 * 1) Административная таблица транзакции состоит из 2-х колонок: «key» и «value».
		 * Нам, разумеется, нужно адаптировать только значения колонки «value».
		 * 2) Источником данных для таблицы является коллекция объектов класса
		 * @see \Magento\Framework\DataObject, которая наполняется в методе
		 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::_prepareCollection()
				foreach ($this->getTransactionAdditionalInfo() as $key => $value) {
					$data = new \Magento\Framework\DataObject(['key' => $key, 'value' => $value]);
					$collection->addItem($data);
				}
		 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L68-L71
		 */
		/** @var bool $apply */
		$apply = 'value' === $columnId && df_trans_is_my();
		return [$columnId, ($apply ? ['renderer' => Text::class] : []) + $column];
	}
}
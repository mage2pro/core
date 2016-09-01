<?php
namespace Df\Backend\Block\Widget\Grid\Column\Renderer;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
// 2016-08-19
class Text extends AbstractRenderer {
	/**
	 * 2016-08-19
	 * @override
	 * @see \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer::render()
	 * @used-by \Magento\Backend\Block\Widget\Grid\Column::getRowField()
	 * @used-by \Df\Sales\Plugin\Block\Adminhtml\Transactions\Detail\Grid::beforeAddColumn()
	 * @param DataObject $row
	 * @return string
	 */
	public function render(DataObject $row) {
		/** @var string|null $v */
		$v = $this->_getValue($row);
		return df_check_json_complex($v) || df_check_xml($v) ? df_tag('pre', [], $v) : $v;
	}
}


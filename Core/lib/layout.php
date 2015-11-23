<?php
use Magento\Framework\View\Element\AbstractBlock;
use \Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;
/**
 * @param string $type
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 * @return AbstractBlock|BlockInterface|Template
 */
function df_block($type, $data = [], $template = null) {
	/** @var string|null $template */
	if (is_string($data)) {
		$template = $data;
		$data = [];
	}
	/** @var AbstractBlock|BlockInterface|Template $result */
	$result = df_layout()->createBlock($type, df_a($data, 'name'), $data);
	if ($template && $result instanceof Template) {
		$result->setTemplate($template);
	}
	return $result;
}

/**
 * @param string $type
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 * @return string
 */
function df_block_r($type, $data = [], $template = null) {
	return df_block($type, $data, $template)->toHtml();
}

/** @return \Magento\Framework\View\Layout|\Magento\Framework\View\LayoutInterface */
function df_layout() {return df_o('Magento\Framework\View\LayoutInterface');}



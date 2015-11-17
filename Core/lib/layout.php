<?php
/**
 * @param string $type
 * @param array(string => mixed) $data [optional]
 * @return \Magento\Framework\View\Element\AbstractBlock|\Magento\Framework\View\Element\BlockInterface
 */
function df_block($type, $data = []) {
	return df_layout()->createBlock($type, df_a($data, 'name'), $data);
}

/**
 * @param string $type
 * @param array(string => mixed) $data
 * @return string
 */
function df_block_r($type, $data = []) {return df_block($type, $data)->toHtml();}

/** @return \Magento\Framework\View\Layout|\Magento\Framework\View\LayoutInterface */
function df_layout() {return df_o('Magento\Framework\View\LayoutInterface');}



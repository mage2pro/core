<?php
/**
 * 2015-10-05
 * @param string $name
 * @param string|null $value
 */
function df_metadata($name, $value) {
	if (!is_null($value) && '' !== $value) {
		df_page()->setMetadata($name, $value);
	}
}

/**
 * 2015-10-05
 * @return \Magento\Framework\View\Page\Config
 */
function df_page() {return df_o(\Magento\Framework\View\Page\Config::class);}
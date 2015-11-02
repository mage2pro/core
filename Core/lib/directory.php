<?php
/** @return \Magento\Directory\Helper\Data */
function df_directory() {return df_o('Magento\Directory\Helper\Data');}

/**
 * @param string $iso2
 * @return bool
 */
function df_is_postcode_required($iso2) {return !df_directory()->isZipCodeOptional($iso2);}



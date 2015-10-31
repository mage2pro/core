<?php
/** @return \Magento\Directory\Helper\Data */
function rm_directory() {return df_o('Magento\Directory\Helper\Data');}

/**
 * @param string $iso2
 * @return bool
 */
function rm_is_postcode_required($iso2) {return !rm_directory()->isZipCodeOptional($iso2);}



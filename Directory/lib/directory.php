<?php
use Magento\Directory\Helper\Data as H;
/**
 * 2016-04-05                               
 * @used-by df_is_postcode_required()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::_validate()
 * @used-by vendor/mage2pro/customer/view/frontend/templates/address/edit.phtml:151
 * @return H
 */
function df_directory() {return df_o(H::class);}

/**
 * @param string $iso2
 * @return bool
 */
function df_is_postcode_required($iso2) {return !df_directory()->isZipCodeOptional($iso2);}


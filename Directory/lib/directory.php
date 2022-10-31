<?php
use Magento\Directory\Helper\Data as H;
/**
 * 2016-04-05                               
 * @used-by df_is_postcode_required()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::_validate()
 * @used-by \KingPalm\B2B\Block\RegionJS\Backend::_toHtml()
 * @used-by \KingPalm\B2B\Block\RegionJS\Frontend::_toHtml()
 * @used-by vendor/mage2pro/customer/view/frontend/templates/address/edit.phtml
 */
function df_directory():H {return df_o(H::class);}

/**
 * 2015-10-12
 * @used-by \Df\Sso\CustomerReturn::register()
 * @used-by \KingPalm\B2B\Setup\V140\MoveDataToAddress::p()
 * @param string $iso2
 */
function df_is_postcode_required($iso2):bool {return !df_directory()->isZipCodeOptional($iso2);}
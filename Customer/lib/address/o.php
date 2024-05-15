<?php
use Magento\Customer\Api\AddressRepositoryInterface as IRep;
use Magento\Customer\Helper\Address as H;
use Magento\Customer\Model\AddressRegistry as Reg;
use Magento\Customer\Model\ResourceModel\AddressRepository as Rep;

/**
 * 2019-06-01
 * @used-by \KingPalm\B2B\Block\Registration::region()
 */
function df_address_h():H {return df_o(H::class);}

/**
 * 2016-04-05
 * @used-by \Df\Customer\Plugin\Model\ResourceModel\AddressRepository::aroundSave()
 * @used-by \Dfe\Customer\Plugin\Customer\Model\ResourceModel\AddressRepository::aroundSave()
 */
function df_address_registry():Reg {return df_o(Reg::class);}

/**
 * 2021-05-07
 * @used-by \Df\Quote\Plugin\Model\QuoteAddressValidator::doValidate()
 * @return IRep|Rep
 */
function df_address_rep() {return df_o(IRep::class);}
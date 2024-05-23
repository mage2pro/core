<?php
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * 2015-10-12
 * @used-by df_customer_att()
 * @used-by df_eav_ca()
 * @used-by df_eav_customer()
 * @used-by df_eav_type()
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Sharapov\Cabinetsbay\Setup\InstallData::install() (https://github.com/cabinetsbay/site/issues/98)
 */
function df_eav_config():Config {return df_o(Config::class);}

/**
 * 2015-10-06
 * @used-by \Df\Customer\AddAttribute\Address::p()
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \KingPalm\B2B\Setup\UpgradeSchema::_process()
 */
function df_eav_setup():EavSetup {return df_o(EavSetup::class);}

/**
 * 2021-03-26
 * @used-by \CanadaSatellite\Theme\Plugin\Model\LinkManagement::aroundSaveChild(canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/44)
 */
function df_metadata_pool():MetadataPool {return df_o(MetadataPool::class);}

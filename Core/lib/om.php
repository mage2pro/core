<?php
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\ObjectManagerInterface as IOM;
use Magento\Framework\ObjectManager\ConfigInterface as IConfig;
use Magento\Framework\ObjectManager\Config\Config;
use Magento\Framework\ObjectManager\Config\Compiled;
use Magento\Framework\Interception\ObjectManager\Config\Developer;

/**
 * 2017-03-20
 * @used-by df_class_exists()
 * @param string $c
 * @return string
 */
function df_ctr($c) {return df_vtr(df_om_config()->getPreference($c));}

/**
 * 2017-03-20
 * @used-by \Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 * @param string $c
 * @return bool
 */
function df_is_virtual($c) {return !!dfa(df_virtual_types(), $c);}

/**
 * @param string $type
 * @return mixed
 */
function df_o($type) {return dfcf(function($type) {return df_om()->get($type);}, func_get_args());}

/**
 * 2015-08-13
 * @used-by df_o()
 * @used-by df_ic()
 * @return OM|IOM
 */
function df_om() {return OM::getInstance();}

/**
 * 2016-05-06
 * @used-by df_class_exists()
 * @used-by df_virtual_types()
 * @used-by \Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 * @return IConfig|Config|Compiled|Developer
 */
function df_om_config() {return df_o(IConfig::class);}

/**
 * 2017-03-20
 * @used-by df_ctr()
 * @used-by \Df\Framework\Plugin\App\Router\ActionList::aroundGet()
 * @param string $c
 * @return string
 */
function df_vtr($c) {return df_om_config()->getInstanceType($c);}

/**
 * 2017-03-20
 * @used-by df_is_virtual()
 * @return array(string => string)
 */
function df_virtual_types() {return df_om_config()->getVirtualTypes();}
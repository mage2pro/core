<?php
use Magento\Framework\App\ActionInterface as IA;

/**
 * @used-by df_url()
 * @used-by df_url_backend()
 * @used-by df_url_frontend()
 * @return array(string => bool)
 */
function df_nosid():array {return ['_nosid' => true];}

/**
 * 2020-01-19
 * @see \Magento\Store\App\Response\Redirect::_getUrl()
 * @used-by \Frugue\Store\Switcher::params()
 * @return array(string => string)
 */
function df_url_param_redirect(string $u = ''):array {return [IA::PARAM_NAME_URL_ENCODED => df_url_h()->getEncodedUrl($u)];}
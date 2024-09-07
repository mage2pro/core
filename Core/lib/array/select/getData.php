<?php
use Magento\Config\Model\Config\Structure\AbstractElement as AE;
use Magento\Framework\DataObject as _DO;

/**
 * 2020-01-29     
 * @used-by df_call()
 * @used-by \Df\Config\Backend::fc()
 * @used-by \Df\Payment\Block\Info::ii()
 * @used-by \Df\Payment\Method::ii()
 * @param _DO|AE $o
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return _DO|AE|mixed
 */
function dfad($o, $k = '', $d = null) {return df_nes($k) ? $o : dfa(df_gd($o), $k, $d);}
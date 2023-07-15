<?php
use Magento\Framework\App\ResponseInterface as IResponse;
use Magento\Framework\App\Response\Http as HttpResponse;

/**
 * 2021-05-24 https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400#content
 * @used-by \MageWorx\OptionInventory\Controller\StockMessage\Update::execute() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/125)
 * @return IResponse|HttpResponse
 */
function df_400() {return df_response_code(400);}

/**
 * 2021-04-19 https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403#content
 * @used-by df_ban()
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Framework\App\Http::aroundLaunch() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/72)
 * @return IResponse|HttpResponse
 */
function df_403() {return df_response_code(403);}

/**
 * 2020-02-24 https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500#content
 * @used-by ikf_endpoint()	inkifi.com
 * @used-by \Dfe\CheckoutCom\Handler::p()
 * @used-by \Dfe\Sift\Controller\Index\Index::execute()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @return IResponse|HttpResponse
 */
function df_500() {return df_response_code(500);}

/**
 * 2015-11-29
 * @used-by df_400()
 * @used-by df_403()
 * @used-by df_500()
 * @return IResponse|HttpResponse
 */
function df_response_code(int $v) {return df_response()->setHttpResponseCode($v);}
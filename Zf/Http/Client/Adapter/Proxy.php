<?php
namespace Df\Zf\Http\Client\Adapter;
/**
 * 2019-01-28
 * It fixes the issue:
 * «SSL operation failed with code 1. OpenSSL Error messages:
 * error:14094410:SSL routines:ssl3_read_bytes:sslv3 alert handshake failure».
 * The issue is caised by the following code in the `magento/zendframework1` package < 1.14.1:
 * https://github.com/magento/zf1/blob/1.14.0/library/Zend/Http/Client/Adapter/Proxy.php#L299-L304
 * https://github.com/magento/zf1/blob/1.13.1/library/Zend/Http/Client/Adapter/Proxy.php#L299-L304
 * I have fixed the issue by using the @see \Zend_Http_Client_Adapter_Proxy::connectHandshake() method
 * from the 1.14.1 version of the `magento/zendframework1` package:
 * https://github.com/magento/zf1/blob/1.14.1/library/Zend/Http/Client/Adapter/Proxy.php#L299-L302
 */
final class Proxy extends \Zend_Http_Client_Adapter_Proxy {
    /**
     * Preform handshaking with HTTPS proxy using CONNECT method
	 * 2022-12-20 We can not declare arguments types because they are undeclared in the overriden method.
     * @overide
	 * @see \Zend_Http_Client_Adapter_Proxy::connectHandshake()
	 * @used-by \Zend_Http_Client_Adapter_Proxy::write()
     * @param string $host
     * @param int $port
     * @param string $http_ver
     * @param array $headers
     * @throws \Zend_Http_Client_Adapter_Exception
     */
    protected function connectHandshake($host, $port = 443, $http_ver = '1.1', array &$headers = []):void {
        $request = "CONNECT $host:$port HTTP/$http_ver\r\n" .
                   "Host: " . $host . "\r\n";

        # Process provided headers, including important ones to CONNECT request
        foreach ($headers as $k => $v) {
            switch (strtolower(substr($v,0,strpos($v,':')))) {
                case 'proxy-authorization':
                    # break intentionally omitted

                case 'user-agent':
                    $request .= $v . "\r\n";
                    break;

                default:
                    break;
            }
        }
        $request .= "\r\n";

        # @see ZF-3189
        $this->connectHandshakeRequest = $request;

        # Send the request
        if (!@fwrite($this->socket, $request)) {
            #require_once 'Zend/Http/Client/Adapter/Exception.php';
            throw new \Zend_Http_Client_Adapter_Exception(
                'Error writing request to proxy server'
            );
        }

        # Read response headers only
        $response = '';
        $gotStatus = false;
        while ($line = @fgets($this->socket)) {
            $gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
            if ($gotStatus) {
                $response .= $line;
                if (!chop($line)) {
                    break;
                }
            }
        }

        # Check that the response from the proxy is 200
        if (\Zend_Http_Response::extractCode($response) != 200) {
            #require_once 'Zend/Http/Client/Adapter/Exception.php';
            throw new \Zend_Http_Client_Adapter_Exception(
                'Unable to connect to HTTPS proxy. Server response: ' . $response
            );
        }

        # If all is good, switch socket to secure mode. We have to fall back
        # through the different modes
        $modes = array(
            # TODO: Add STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT in the future when it is supported by PHP
            STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
        );

        $success = false;
        foreach($modes as $mode) {
            $success = stream_socket_enable_crypto($this->socket, true, $mode);
            if ($success) {
                break;
            }
        }

        if (!$success) {
            #require_once 'Zend/Http/Client/Adapter/Exception.php';
            throw new \Zend_Http_Client_Adapter_Exception(
                'Unable to connect to HTTPS server through proxy: could not '
                . 'negotiate secure connection.'
            );
        }
    }
}
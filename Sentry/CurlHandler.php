<?php
namespace Df\Sentry;
// TODO(dcramer): handle ca_cert
class CurlHandler
{
    private $join_timeout;
    private $multi_handle;
    private $options;
    private $requests;

    function __construct($options, $join_timeout=5)
    {
        $this->options = $options;
        $this->multi_handle = curl_multi_init();
        $this->requests = [];
        $this->join_timeout = 5;

        register_shutdown_function(array($this, 'join'));
    }

    function __destruct()
    {
        $this->join();
    }

    function enqueue($url, $data=null, $headers=[])
    {
        $ch = curl_init();

        $new_headers = [];
        foreach ($headers as $key => $value) {
            array_push($new_headers, $key .': '. $value);
        }
        // XXX(dcramer): Prevent 100-continue response form server (Fixes GH-216)
        $new_headers[] = 'Expect:';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $new_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt_array($ch, $this->options);

        if (isset($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_multi_add_handle($this->multi_handle, $ch);

        $fd = (int)$ch;
        $this->requests[$fd] = 1;

        $this->select();

        return $fd;
    }

    function join($timeout=null)
    {
        if (!isset($timeout)) {
            $timeout = $this->join_timeout;
        }
        $start = time();
        do {
            $this->select();
            if (count($this->requests) === 0) {
                break;
            }
            usleep(10000);
        } while ($timeout !== 0 && time() - $start < $timeout);
    }

    // http://se2.php.net/manual/en/function.curl-multi-exec.php
    private function select()
    {
        do {
            $mrc = curl_multi_exec($this->multi_handle, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->multi_handle) !== -1) {
                do {
                    $mrc = curl_multi_exec($this->multi_handle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            } else {
                return;
            }
        }

        while ($info = curl_multi_info_read($this->multi_handle)) {
            $ch = $info['handle'];
            $fd = (int)$ch;

            curl_multi_remove_handle($this->multi_handle, $ch);

            if (!isset($this->requests[$fd])) {
                return;
            }

            unset($this->requests[$fd]);
        }
    }
}

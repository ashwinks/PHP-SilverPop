<?php
/**
 * Created by ashwin@redinkdesign.net
 */
namespace Silverpop;

class Client
{
    protected $_api_endpoint = null;
    protected $_debug;
    protected $_curl_handle;

    public function __construct($pod, $debug = false)
    {
        $this->_pod = $pod;
        $this->_debug = $debug;
    }

    public function getApiEndpoint()
    {
        if (!$this->_api_endpoint){
            $this->_api_endpoint = "http://transact{$this->_pod}.silverpop.com/XTMail";
        }

        return $this->_api_endpoint;
    }

    public function setDebug($is_debug)
    {
        $this->_debug = $is_debug;

        return $this;
    }

    public function isDebug()
    {
        return $this->_debug;
    }

    public function makeRequest($payload, $method = 'POST', array $headers = array(), array $curl_options = array())
    {
        $ch = $this->_getCurlHandle();
        $method = strtoupper($method);

        $options = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->getApiEndpoint(),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
        );

        $headers[] = 'Content-Length: ' . strlen($options[CURLOPT_POSTFIELDS]);
        $options[CURLOPT_HTTPHEADER] = $headers;

        if (!empty($curl_options)){
            $options = array_merge($options, $curl_options);
        }

        curl_setopt_array($ch, $options);
        $this->_raw_response = curl_exec($ch);
        $this->_debug_info = curl_getinfo($ch);

        if ($this->_raw_response === false){
            throw new \RuntimeException('Request Error: ' . curl_error($ch));
        }

        if ($this->_debug_info['http_code'] < 200 || $this->_debug_info['http_code'] >= 400){
            throw new \RuntimeException('API Request failed - Response: ' . $this->_raw_response, $this->_debug_info['http_code']);
        }

        return $this->_raw_response;
    }

    /**
     * Singleton to get a CURL handle
     *
     * @return resource
     */
    protected function _getCurlHandle(){

        if (!$this->_curl_handle){
            $this->_curl_handle = curl_init();
        }

        return $this->_curl_handle;

    }

    /**
     * Closes the currently open CURL handle
     */
    public function __destruct(){

        if ($this->_curl_handle){
            curl_close($this->_curl_handle);
        }

    }
}

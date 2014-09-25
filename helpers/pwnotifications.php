<?php

/**
 * Use this lib for calling pushwoosh api
 *
 * @author Dmitriy Kuzhel <kuzheld@gmail.com>
 * @date 14.03.2014
 * 
 * @package Room8
 * 
 */
class PWNotifications
{
    /**
     * Api url
     */
    const API_URL = "https://cp.pushwoosh.com/json/1.3/";

    /**
     * Send post request to pushwoosh
     * @param String $url - pushwoosh api url
     * @param Array $data - params data array
     * @param NULL | array $optional_headers - additional headers options
     * @return boolean
     * @throws Exception
     */
    private function _doPostRequest($url, $data, $optional_headers = null, $action = false){
        $data               = json_encode($data);
        $errorn             = "";
        $error_str          = "";
        $connectionTimeout  = 2;
        $fp                 = fsockopen("ssl://cp.pushwoosh.com", 443, $errorn, $error_str, $connectionTimeout);
        
        socket_set_blocking($fp, false);
        stream_set_timeout($fp, $connectionTimeout);
        
        if ($fp) {
            $output = "POST /json/1.3/$action  HTTP/1.1\r\n";
            $output .= "Host: cp.pushwoosh.com\r\n";
            $output .= "Connection: Close\r\n";
            $output .= "Content-Type: application/json\r\n";
            $output .= "Content-Length: " . strlen($data) . " \r\n\r\n";
            $output .= $data; //. "\r\n";
            fwrite($fp, $output);

            while (!feof($fp)) {
                    echo fgets($fp, 128);
            }
        }

        fclose($fp);
    }

    /**
     * Make api call
     * 
     * @param String $action - api name
     * @param Array $data - data to send
     * @return void
     */
    public function call($action, $data = array()){
        $url    = self::API_URL . $action;
        $json   = array('request' => $data);
        
        return $this->_doPostRequest($url, $json, 'Content-Type: application/json', $action);
    }
}

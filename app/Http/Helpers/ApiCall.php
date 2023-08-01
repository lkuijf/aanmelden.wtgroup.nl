<?php
namespace App\Http\Helpers;

class ApiCall {
    public $id;
    public $endpoint;
    public $method = 'GET';
    public $payload;
    public $parameters;
    public $headers = array();
    public $httpBasicAuth;
    public $parent;
    public $res;
    public function __construct() {
    }
    public function get() {
        $curl = curl_init();

        $params = '';
        if($this->parameters) $params = '?' . http_build_query($this->parameters);

// echo "\n" . '[' . config('app_wt.cmsPath') . $this->endpoint . $params.']' . "<br />\n";

        curl_setopt($curl, CURLOPT_URL, config('app_wt.cmsPath') . $this->endpoint . $params);
        if($this->method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->payload);
        }
        if($this->method == 'PUT') {
            curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->payload);
        }
        if(count($this->headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        if($this->httpBasicAuth) curl_setopt($curl, CURLOPT_USERPWD, $this->httpBasicAuth['username'] . ":" . $this->httpBasicAuth['password']);
        // $check_ping_resolve = ["mironmarine.nl:80:10.250.8.10"];
        // curl_setopt($curl, CURLOPT_RESOLVE, $check_ping_resolve);

        $data = curl_exec($curl);
        $headers = curl_getinfo($curl);

// echo 'Lookup time: ' . ($headers['namelookup_time'] * 1000) . 'ms' . "<br />\n";
// echo 'Connect time: ' . ($headers['connect_time'] * 1000) . 'ms' . "<br />\n";
// echo 'Pretransfer time: ' . ($headers['pretransfer_time']) . 'ms' . "<br />\n";
// echo 'Redirect time: ' . ($headers['redirect_time'] * 1000) . 'ms' . "<br />\n";
// echo 'Time to 1st Byte time: ' . ($headers['starttransfer_time'] * 1000) . 'ms' . "<br />\n";
// echo 'Total time: ' . ($headers['total_time'] * 1000) . 'ms' . "<br />\n";

        curl_close($curl);
    
        $this->res = json_decode($data);
        $this->postProcess(); // for additional processing of the data
        return $this->res;
    }
    public function postProcess() {}
    public function setParent($id) {
        $this->parent = $id;
    }
}

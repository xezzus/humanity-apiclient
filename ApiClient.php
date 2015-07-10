<?php
namespace humanity;

/*
 * * * CONFIG * * *
 *
 * url - ...
 * app_id - ...
 * sig - ...
 *
 * * * EXAMPLE * * *
 *
 * $client = new humanity\ApiClient([
 *      'url'=>'http://example.com'
 *      'app_id'=>'123',
 *      'sig'=>'as897asf897asdf789asdf897asdf987asdf'
 * ]);
 *
 * $html = $client->widget([
 *      'method'=>'name.method',
 *      'params':[
 *          'param1':'123',
 *          'param2':'321'
 *       ]
 * ]);
 *
 * $array = $client->apps([
 *      'method'=>'name.app',
 *      'params':[
 *          'param1':'123',
 *          'param2':'321'
 *       ]
 * ]);
 *
 */

class ApiClient {

    private $config;

    public function __construct($config){
        $this->config = $config;
    }

    private function send($method,$params=[],$type){
        if(!is_array($params)) return false;
        $posts = [
            'method'=>$method,
            'params'=>$params
        ];
        if(isset($this->config['app_id'])) {
            $posts['app_id'] = $this->config['app_id'];
        }
        if(isset($this->config['sig'])) {
            $posts['sig'] = $this->config['sig'];
        }

        if($type == 'apps'){
            $accept = 'Accept: application/apps';
        } else if($type == 'widget'){
            $accept = 'Accept: application/widget';
        } else {
            $accept = '*/*';
        }

        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL=>$this->config['url'],
            CURLOPT_HEADER=>false,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_POST=>true,
            CURLOPT_VERBOSE=>false,
            CURLOPT_CONNECTTIMEOUT=>3,
            CURLOPT_TIMEOUT=>3,
            CURLOPT_USERAGENT=>"humanity",
        ]);
        curl_setopt($curl,CURLOPT_HTTPHEADER,[$accept]);
        $cookie = [];
        foreach($_COOKIE as $name=>$value){
            $cookie[] = urlencode($name).'='.urlencode($value);
        }
        $cookie = implode('; ',$cookie);
        curl_setopt($curl,CURLOPT_COOKIE,$cookie);
        curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($posts));
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public function apps($query){
        if(!isset($query['params'])) $query['params'] = [];
        $data = $this->send($query['method'],$query['params'],'apps');
        $json = json_decode($data,true);
        if(!is_array($json)) {
            return false;
        } else {
            return $json;
        }
    }

    public function widget($query){
        if(!isset($query['params'])) $query['params'] = [];
        return $this->send($query['method'],$query['params'],'widget');
    }

}
?>

<?php
class PushWoosh
{
    protected $config;
    
    private $logParams = array(
        'message'    => '', 
        'method'     => 'CLASS PUSH WOOSH', 
        'fail'       => false, 
        'mysqlError' => false, 
        'userId'     => 'undefined', 
    );

    public function __construct($appId, $auth){
        $config = array(
            'application'   => $appId,
            'auth'          => $auth,
        );
        
        $this->config = $config;
    }

    private function pwCall($method, $data){
        $url        = 'https://cp.pushwoosh.com/json/1.3/' . $method;
        $request    = json_encode(array('request' => $data));
        $ch         = curl_init($url);

        $this->logParams['message'] = $request;
        writeInErroLog($this->logParams);
        
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request))
        );

        $response   = curl_exec($ch);
        $error      = curl_error($ch);

        if($error) {
            $info = curl_getinfo($ch);
        }

        curl_close($ch);
        return !empty($error) ? json_decode($info, true) : json_decode($response, true);
    }

    public function createMessage(array $pushes, $sendDate = 'now', $link = null, $ios_badges = 10){
        $config = $this->config;
        $data   = array(
            'application'   => $config['application'],
            'auth'          => $config['auth']
        );

        foreach ($pushes as $push) {
            $pushData = array(
                'send_date'     => $sendDate,
                'content'       => $push['content'],
                'ios_badges'    => $ios_badges
            );

            if (array_key_exists('devices', $push)) {
                $pushData['devices'] = $push['devices'];
            }
            
            if (array_key_exists('data', $push)) {
                $pushData['data'] = $push['data'];
            }

            if ($link) {
                $pushData['link'] = $link;
            }
            
            $data['notifications'][] = $pushData;
        }

        $response = $this->pwCall('createMessage', $data);
        return !empty($response) ? $response : array('error'=>'502 Bad Gateway') ;
    }
}
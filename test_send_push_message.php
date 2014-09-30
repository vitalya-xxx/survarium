<?php
require("config.inc.php");
require("helpers/PushWoosh.php");

$user_token = isset($_POST['user_token']) ? $_POST['user_token'] : null;
$message    = isset($_POST['message']) ? $_POST['message'] : '';
$pw2        = new PushWoosh(APPLICATION_CODE, API_ACCESS);

$logParams = array(
    'message'    => '', 
    'method'     => 'TEST_SEND_PUSH_MESSAGE', 
    'fail'       => true, 
    'mysqlError' => false, 
    'userId'     => 'undefined', 
);

if (!empty($user_token)) {
    $message = !empty($message) ? $message : 'Test push message.';
    $message = 'Test author: '.$message;

    $pushes = array(
        array(
            'content' => $message,
            'devices' => array($user_token),
            'data'    => array(
                'custom' => "{'type':1,'user_id':777,'room_id':888}"
            ),
        ),
    );

    $response = $pw2->createMessage($pushes);

    $logParams['message'] = 'send PushWoosh'.json_encode($response);
    $logParams['fail']    = false;
    writeInErroLog($logParams);
    
    echo json_encode($response);
}
else {
    sendError(6);
}


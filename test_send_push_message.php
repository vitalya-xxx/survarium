<?php
require("config.inc.php");
require("helpers/PushWoosh.php");

$user_token = isset($_POST['user_token']) ? $_POST['user_token'] : null;
$message    = isset($_POST['message']) ? $_POST['message'] : '';
$pw2        = new PushWoosh(APPLICATION_CODE, API_ACCESS);

if (!empty($user_token)) {
    $message = !empty($message) ? $message : 'Test push message.';
    $message = 'Test author: '.$message;
    
    $pushes = array(
        array(
            'content' => $message,
            'devices' => array($user_token),
        ),
    );
    
    $response = $pw2->createMessage($pushes);
    echo json_encode($response);
}
else {
    sendError(6);
}


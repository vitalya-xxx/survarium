<?php
require("config.inc.php");

$nickname  = isset($_POST['user_nickname']) ? $_POST['user_nickname'] : null;
$password  = isset($_POST['user_password']) ? $_POST['user_password'] : null;
$user_id   = isset($_POST['id']) ? $_POST['id'] : null;

$logParams = array(
    'message'    => '', 
    'method'     => 'edit_login_data', 
    'fail'       => true, 
    'mysqlError' => false, 
    'userId'     => $nickname.'/'.$password.'/'.$user_id, 
);

$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $user_id);

if (!empty($nickname) && !empty($password) && !empty($user_id)) {
    $nickname   = $sqlDriver->prepareData($nickname);
    $password   = $sqlDriver->prepareData($password);
    $user_id    = (int)$sqlDriver->prepareData($user_id);
    
    $sql = "
        SELECT *
        FROM users
        WHERE user_nickname = '".$nickname."'
    ";

    $userByNickName = $sqlDriver->Select($sql);

    if (!empty($userByNickName[0]['user_id'])) {
        if ($user_id == (int)$userByNickName[0]['user_id']) {
            $result = $sqlDriver->Update('users', array('user_password' => $password, 'confirm' => 1), 'user_id = '.$user_id);
            if ($result) {
                $logParams['message']   = 'user to activate your account';
                $logParams['fail']      = false;
                writeInErroLog($logParams); 
                echo json_encode(array('result' => 'ok'));
            }
            else {
                $logParams['message'] = 'sendError(5)';
                writeInErroLog($logParams); 
                sendError(5);
            }
        }
        else {
            $logParams['message'] = 'sendError(4)';
            writeInErroLog($logParams); 
            sendError(4);
        }
    }
    else {
        $result = $sqlDriver->Update(
            'users', 
            array('user_nickname' => $nickname, 'user_password' => $password, 'confirm' => 1), 
            'user_id = '.$user_id
        );
        
        if ($result) {
            $logParams['message']   = 'user to activate your account';
            $logParams['fail']      = false;
            writeInErroLog($logParams); 
            echo json_encode(array('result' => 'ok'));
        }
        else {
            $logParams['message'] = 'sendError(5)';
            writeInErroLog($logParams); 
            sendError(5);
        }
    }
}
else {
    $logParams['message'] = 'sendError(6)';
    writeInErroLog($logParams); 
    sendError(6); 
}

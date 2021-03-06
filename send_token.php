<?php
require("config.inc.php");

$id             = (isset($_POST['id'])) ? (int)$_POST['id'] : null;
$user_token     = (isset($_POST['user_token'])) ? trim($_POST['user_token']) : null;
$user_token_old = (isset($_POST['user_token_old'])) ? trim($_POST['user_token_old']) : null;
$sqlDriver      = new SQLDriver();

$logParams = array(
    'message'    => '', 
    'method'     => 'SEND_TOKEN', 
    'fail'       => false, 
    'mysqlError' => false, 
    'userId'     => $id, 
);

UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $id);

if (!empty($id) && (!empty($user_token) || !empty($user_token_old))) {
    $user = $sqlDriver->Select("SELECT * FROM users WHERE user_id = ".$id);
    
    // проверка существования пользователя
    if (!empty($user[0])) {
        $user_token         = str_replace(array("<",">"), "", $user_token);
        $deviceTokensStr    = $user[0]['device_token'];
        $deviceTokensArr    = !empty($deviceTokensStr) ? explode(",", $deviceTokensStr) : array();

        // надо заменить токен
        if (!empty($user_token) && !empty($user_token_old)) {
            $logParams['message'] = 'replacement '.$user_token_old.' on '.$user_token;
            writeInErroLog($logParams);
            
            $newTokens = array();
            
            if (!empty($deviceTokensArr)) {
                foreach ($deviceTokensArr as $token) {
                    if ($token == $user_token_old) {
                        $newTokens[] = $user_token;
                    }
                    else {
                        $newTokens[] = $token;
                    }
                }
                $deviceTokensArr = array();
                $deviceTokensArr = $newTokens;
            }
            else {
                $deviceTokensArr[] = $user_token;
            }
        }
        // добавить новый
        else if (!empty($user_token) && empty($user_token_old)) {
            
            $logParams['message'] = 'add new token '.$user_token;
            writeInErroLog($logParams);
            
            $tokenExists = false;
            foreach ($deviceTokensArr as $token) {
                if ($token == $user_token) {
                    $tokenExists = true;
                }
            }
            if (!$tokenExists) {
                array_push($deviceTokensArr, $user_token);
            }
        }
        // удалить токен
        else if (empty($user_token) && !empty($user_token_old)) {
            
            $logParams['message'] = 'remove token '.$user_token;
            writeInErroLog($logParams);
            
            $newTokens = array();
            
            foreach ($deviceTokensArr as $token) {
                if ($token == $user_token_old) {
                    continue;
                }
                else {
                    $newTokens[] = $token;
                }
            }
            $deviceTokensArr = array();
            $deviceTokensArr = $newTokens;
        }

        $deviceTokensStr = !empty($deviceTokensArr) ? implode(",", $deviceTokensArr) : '';
        
        $object = array(
            'device_token' => $deviceTokensStr,
        );

        $result = $sqlDriver->Update('users', $object, 'user_id = '.$id);

        if (false !== $result) {
            $logParams['message'] = 'DB UPDATED '.json_encode($object);
            writeInErroLog($logParams);
            sendSuccess();
        }
        else {
            $logParams['message']   = 'sendError(5)';
            $logParams['fail']      = true;
            writeInErroLog($logParams);
            sendError(5);
        }
    }
    else {
        $logParams['message']   = 'sendError(7)';
        $logParams['fail']      = true;
        writeInErroLog($logParams);
        sendError(7);
    }
}
else {
    $logParams['message']   = 'sendError(6)';
    $logParams['fail']      = true;
    writeInErroLog($logParams);
    sendError(6);
}


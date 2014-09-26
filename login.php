<?php
require("config.inc.php");

$nickname = $_POST['user_nickname'];
$password = $_POST['user_password'];

$logParams = array(
    'message'    => '', 
    'method'     => 'LOGIN', 
    'fail'       => true, 
    'mysqlError' => false, 
    'userId'     => $nickname.'/'.$password, 
);

if (empty($nickname) || empty($password)) {
    $logParams['message'] = 'sendError(6)';
    writeInErroLog($logParams);
    sendError(6);
}
$query = "SELECT * FROM users WHERE user_nickname = '$nickname'";
$res = mysql_query($query, $db);

if (!$res) {
    $logParams['message']       = mysql_error()." < ".$query." >";
    $logParams['mysqlError']    = true;
    writeInErroLog($logParams);
    die();
}
else {
    $row = mysql_fetch_assoc($res);     

    if (!empty($row) && ($password == $row['user_password'])) {
        $response["id"] = $row['user_id'];
        $query          = "UPDATE users SET user_isOnline='1' WHERE user_nickname = '$nickname'";
        $result         = mysql_query ($query, $db);
        
        if (!$result) {
            $logParams['message']       = mysql_error()." < ".$query." >";
            $logParams['mysqlError']    = true;
            writeInErroLog($logParams);
            die();
        }
        else {
            $sqlDriver  = new SQLDriver();
            UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $response["id"]);
            
            $logParams['message']   = 'user logged / '.json_encode($response);
            $logParams['fail']      = false;
            writeInErroLog($logParams);
            
            die(json_encode($response));
        }
    }
    else {
        $logParams['message'] = 'sendError(8)';
        writeInErroLog($logParams);
        sendError(8);
    }
}


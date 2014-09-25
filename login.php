<?php
require("config.inc.php");

$nickname = $_POST['user_nickname'];
$password = $_POST['user_password'];

if (empty($nickname) || empty($password)) {
    $response["errorCode"] = "Empty"; 
    die(json_encode($response));
}

$res = mysql_query("SELECT * FROM users WHERE user_nickname = '$nickname'", $db);
$row = mysql_fetch_assoc($res);     

if (!empty($row) && ($password == $row['user_password'])) {
    $response["id"] = $row['user_id'];
    $result         = mysql_query ("UPDATE users SET user_isOnline='1' WHERE user_nickname = '$nickname'",$db);
    
    $sqlDriver  = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $response["id"]);
    
    die(json_encode($response));
}
else {
    sendError(8);
}


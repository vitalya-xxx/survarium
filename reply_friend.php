<?php
require("config.inc.php");
require("helpers/MemcacheClass.php");

$logParams = array(
    'message'    => '', 
    'method'     => 'REPLY_FRIEND', 
    'fail'       => false, 
    'mysqlError' => false, 
    'userId'     => '', 
);

function writeCountInMemcache($user_id){
    global $logParams;
    
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $count          = !empty($countMemcache) ? ($countMemcache['count'] - 1) : 0;
    $result         = (0 < $count) ? $count : 0;
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $result));
    
    $logParams['message'] = '[3/4] WRITE_IN_MEM / MEM_KEY - '.KEY_REQUEST_COUNT.$user_id;
    $logParams['message'] .= '[4/5] OLD_VAL - '.$countMemcache['count'];
    $logParams['message'] .= '[5/6] NEW_VAL - '.$result;
    writeInErroLog($logParams);
    
    return true;
}

function addFriends( $user,$friends ) {
    global $logParams;
    
    $sql1 = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) VALUES ('".$user."','".$friends."')");
    $sql2 = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) VALUES ('".$friends."','".$user."')");	
    
    $response['errorCode'] = ($sql1 && $sql2) ? 'true' : 'false'; 	

    $logParams['message'] .= '[2] addFriends '.json_encode($response);
    writeInErroLog($logParams);
    return $response;
}

function inviteFriendsDelete($user, $friends, $addFriend = false) {
    global $logParams;
	$sql = mysql_query("DELETE FROM invite WHERE user_id = '$friends' AND user_id_friend = '$user' ");
    
    $logParams['message'] = '[2/3] inviteFriendsDelete ';
    writeInErroLog($logParams);
    
    if ($addFriend) {
        $sql = "
            SELECT COUNT(id) AS count
            FROM invite
            WHERE user_id = ".$user." 
                AND user_id_friend = ".$friends."
        ";
        
        $result = mysql_query($sql);
        
        if ($result) {
            $result = mysql_fetch_assoc($result);
        }
        
        if (0 < (int)$result['count']) {
            $sql = "
                DELETE 
                FROM invite 
                WHERE user_id = ".$user."
                    AND user_id_friend = ".$friends." 
            ";
            $result = mysql_query($sql);
            
            if ('on' == MEMCACHE_STATE) {
                writeCountInMemcache((int)$friends);
            }
        }
    }
}

if($_POST["id"]&&$_POST["user_id"]){
    $logParams['userId']    = 'from: '.$_POST["id"].'/room: '.$_POST["user_id"];
    $sqlDriver              = new SQLDriver();
    
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $_POST["id"]);
    
    if($_POST["reply"] == 1){
        $logParams['message'] = '[1] Reaply - 1 / ';
        $result = addFriends($_POST["id"],$_POST["user_id"]);
        if ($result) {
            inviteFriendsDelete($_POST["id"], $_POST["user_id"], true);
            if ('on' == MEMCACHE_STATE) {
                writeCountInMemcache((int)$_POST["id"]);
            }
            sendSuccess();
        }
        else {
            sendError(5);
        }
    } 
    else {
        $logParams['message'] = '[1] Reaply - 0 / ';
        writeInErroLog($logParams);
        
        inviteFriendsDelete($_POST["id"],$_POST["user_id"]);
        if ('on' == MEMCACHE_STATE) {
            writeCountInMemcache((int)$_POST["id"]);
        }
        sendSuccess();
    }
}
else {
    sendError(6);
}



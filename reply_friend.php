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
    $logParams['message'] = 'WRITE_IN_MEM / MEM_KEY - '.KEY_REQUEST_COUNT.$user_id;
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $logParams['message'] .= ' OLD_VAL - '.$countMemcache['count'];
    $count          = !empty($countMemcache) ? ($countMemcache['count'] - 1) : 0;
    $result         = (0 < $count) ? $count : 0;
    $logParams['message'] .= ' NEW_VAL - '.$result;
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $result));
    writeInErroLog($logParams);
    return true;
}

function addFriends( $user,$friends ) {
    global $logParams;
    
    $sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$user."','".$friends."')");
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$friends."','".$user."')");		
		if($sql){$response = array ('errorCode' => 'true');} else {$response = array ('errorCode' => 'false');}	
        
        $logParams['message'] .= ' addFriends '.json_encode($response);
        writeInErroLog($logParams);
		return $response;
}
function inviteFriendsDelete( $user,$friends ) {
    global $logParams;
	$sql = mysql_query("DELETE FROM invite WHERE user_id = '$friends' AND user_id_friend = '$user' ");
    $logParams['message'] .= ' inviteFriendsDelete ';
    writeInErroLog($logParams);
		
}

if($_POST["id"]&&$_POST["user_id"]){
    $logParams['userId']    = 'from: '.$_POST["id"].'/room: '.$_POST["user_id"];
    $sqlDriver              = new SQLDriver();
    
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $_POST["id"]);
    
    if($_POST["reply"] == 1){
        $logParams['message'] = 'Reaply - 1 / ';
        $result = addFriends($_POST["id"],$_POST["user_id"]);
        if ($result) {
            inviteFriendsDelete($_POST["id"],$_POST["user_id"]);
        }
        echo json_encode(array('addFriends'=>'ok'));
    } 
    else {
        $logParams['message'] = 'Reaply - 0 / ';
        inviteFriendsDelete($_POST["id"],$_POST["user_id"]);
        echo json_encode(array('inviteFriendsDelete'=>'ok'));
    }
    
    if ('on' == MEMCACHE_STATE) {
        writeCountInMemcache((int)$_POST["id"]);
    }
}
else {
    sendError(6);
}



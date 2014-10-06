<?php
require("config.inc.php");
require("helpers/MemcacheClass.php");

$logParams = array(
    'message'    => '', 
    'method'     => 'ADD_FRIEND', 
    'fail'       => false, 
    'mysqlError' => false, 
    'userId'     => '', 
);

function addFriends( $user,$friends ) {
    global $logParams;
    $sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$user."','".$friends."')");
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$friends."','".$user."')");	
        
    $logParams['message'] = '[1] ADD ROW IN DB';
    writeInErroLog($logParams);    
		
}
function inviteFriendsDelete($user, $friends) {
    global $logParams;
    $sql = "
        DELETE 
        FROM invite 
        WHERE (user_id = ".$user." AND user_id_friend = ".$friends.")
            OR (user_id = ".$friends." AND user_id_friend = ".$user.")
    ";
    
	$result = mysql_query($sql);
    
    $logParams['message'] = '[2] inviteFriendsDelete';
    writeInErroLog($logParams); 
}

function writeCountInMemcache($user_id){
    global $logParams;
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $count          = !empty($countMemcache) ? ($countMemcache['count'] - 1) : 0;
    $result         = (0 < $count) ? $count : 0;
    
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $result));
    
    $logParams['message'] = '[3] WRITE IN THE MEMCACHE / MEM_KEY - '.KEY_REQUEST_COUNT.$user_id.' / OLD_VAL - '.$countMemcache['count'].' / NEW_VAL - '.$result;
    writeInErroLog($logParams);
    
    return true;
}

if($_POST["id"]&&$_POST["user_id"]){
    $id         = isset($_POST['id']) ? (int)$_POST['id'] : null; 
    $user_id    = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null; 
    
    $logParams['userId'] = 'from: '.$id.'/to: '.$user_id;
    
    $sqlDriver  = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $id);
    
    if (!empty($id) && !empty($user_id)) {
        addFriends($id, $user_id);
        inviteFriendsDelete($id, $user_id);
        
        if ('on' == MEMCACHE_STATE) {
            writeCountInMemcache($id);
            writeCountInMemcache($user_id);
        }
        
        sendSuccess();
    }
    else {
        sendError(6);
    }
}


?> 


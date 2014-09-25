<?php
require("config.inc.php");
require("helpers/MemcacheClass.php");

function addFriends( $user,$friends ) {
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$user."','".$friends."')");
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$friends."','".$user."')");		
		
}
function inviteFriendsDelete($user, $friends) {
    $sql = "
        DELETE 
        FROM invite 
        WHERE (user_id = ".$user." AND user_id_friend = ".$friends.")
            OR (user_id = ".$friends." AND user_id_friend = ".$user.")
    ";
    
	$result = mysql_query($sql);
}

function writeCountInMemcache($user_id){
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $count          = !empty($countMemcache) ? ($countMemcache['count'] - 1) : 0;
    $result         = (0 < $count) ? $count : 0;
    
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $result));
    return true;
}

if($_POST["id"]&&$_POST["user_id"]){
    $id         = isset($_POST['id']) ? (int)$_POST['id'] : null; 
    $user_id    = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null; 
    
    $sqlDriver  = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $id);
    
    if (!empty($id) && !empty($user_id)) {
        addFriends($id, $user_id);
        inviteFriendsDelete($id, $user_id);
        
        if ('on' == MEMCACHE_STATE) {
            writeCountInMemcache($id);
        }
    }
    else {
        sendError(6);
    }
}


?> 


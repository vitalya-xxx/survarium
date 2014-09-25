<?php
require("config.inc.php");


function addFriends( $user,$friends ) {
	
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$user."','".$friends."')");
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$friends."','".$user."')");		
		if($sql){$response = array ('errorCode' => 'true');} else {$response = array ('errorCode' => 'false');}											
		return $response;
}
function inviteFriendsDelete( $user,$friends ) {

	$sql = mysql_query("DELETE FROM invite WHERE user_id = '$friends' AND user_id_friend = '$user' ");
		
}


if($_POST["id"]&&$_POST["user_id"]){
    $sqlDriver = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $_POST["id"]);
    
    if($_POST["reply"] == 1){
        $result = addFriends($_POST["id"],$_POST["user_id"]);
        if ($result) {
            inviteFriendsDelete($_POST["id"],$_POST["user_id"]);
        }
        die(json_encode(array('addFriends'=>'ok')));
    } 
    else {
        inviteFriendsDelete($_POST["id"],$_POST["user_id"]);
        die(json_encode(array('inviteFriendsDelete'=>'ok')));
    }	
}
else {
    sendError(6);
}



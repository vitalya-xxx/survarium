<?php
require("config.inc.php");
require("helpers/helpFunctionForRooms.php");

$id      = (isset($_POST['id'])) ? (int)$_POST['id'] : null;
$user_id = (isset($_POST['user_id'])) ? (int)$_POST['user_id'] : null;

if (!empty($id) || !empty($user_id)) {
    $sqlDriver  = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $id);
    
    $userOne = isExists('users', 'user_id', array('user_id'=>$id));
    $userTwo = isExists('users', 'user_id', array('user_id'=>$user_id));
    
    if (empty($userOne) || empty($userTwo)) {
        sendError(7);
    }
    else {
        $room_id = roomExists($id, $user_id);

        if (empty($room_id)) {
            $room_id = createRoom($id, $user_id);
        }

        $response = array();

        if (!empty($room_id)) {
            $response[] = array(
                'room_id'           => $room_id,
                'room_last_message' => getLastMessageForRooms($room_id),
                'room_participants' => getParticipantsForRooms($room_id, $id),
                'room_messages'     => getRoomMessages($room_id),
            );
        }
        echo json_encode($response);
    }
}    
else {
    sendError(6);
}




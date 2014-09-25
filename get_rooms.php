<?php
require("config.inc.php");
require("helpers/helpFunctionForRooms.php");

$user_id    = (isset($_POST['id'])) ? (int)$_POST['id'] : null;
$page       = (isset($_POST['page'])) ? (int)$_POST['page'] : null;
$filter     = (isset($_POST['filter'])) ? $_POST['filter'] : array();
$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $user_id);

if (!empty($user_id)) {
    $limitWhere     = '';
    $filterWhere    = '';
    
    if (!empty($page)) {
        $countUserRooms = null;
        
        $sql = "
            SELECT COUNT(*)
            FROM rooms
            WHERE user_id = ".$user_id." OR user_id_friend = ".$user_id."
        ";
        
        $result = mysql_query($sql);

        if ($result) {
            $count = mysql_fetch_array($result);
            $countUserRooms = $count[0];
        }
        
        if (!empty($countUserRooms)) {
            $start1 = (ROOMS_ON_PAGE * $page) - ROOMS_ON_PAGE;
            $start2 = ($countUserRooms > $start1) ? $start1 : ($countUserRooms - ROOMS_ON_PAGE);
            $start2 = (0 < $start2) ? $start2 : 0;
            
            $limitWhere .= "LIMIT ".$start2.", ".ROOMS_ON_PAGE;
        }
    }
    
    if (!empty($filter['user_nickname'])) {
        $user_nickname  = trim(htmlspecialchars($filter["user_nickname"]));
        $user_nickname  = mysql_real_escape_string($user_nickname);        
        $filterWhere    .= "
            INNER JOIN users AS u
                ON u.user_id != ".$user_id." 
                AND (u.user_id = r.user_id OR u.user_id = r.user_id_friend)
                AND u.user_nickname LIKE '".$user_nickname."%'
        ";
    }
    
    $sql = "
        SELECT r.id
        FROM rooms AS r
            ".$filterWhere."
        WHERE r.user_id = ".$user_id." OR r.user_id_friend = ".$user_id."
            ".$limitWhere."
    ";

    $result = mysql_query($sql);
    $rooms  = array();
    
    if (mysql_num_rows($result)) {
        while ($row = mysql_fetch_assoc($result)) {
            $rooms[]['room_id'] = (int)$row['id'];
        }
        mysql_free_result($result);
    }
    
    $response['rooms'] = array();
    
    foreach ($rooms as $room) {
        $room_participants = getParticipantsForRooms($room['room_id'], $user_id); 
        
        if (empty($room_participants)) {
            continue;
        }
        else {
            $response['rooms'][] = array(
                'room_id'           => $room['room_id'],
                'room_last_message' => getLastMessageForRooms($room['room_id']),
                'room_participants' => $room_participants,
            );
        }
    }
    
    echo json_encode($response);
}    
else {
    sendError(6);
}


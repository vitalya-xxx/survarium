<?php
require("config.inc.php");

$user_id    = (isset($_POST['id'])) ? $_POST['id'] : null;
$room_id    = (isset($_POST['room_id'])) ? $_POST['room_id'] : 0;
$data_page  = (isset($_POST['data_page'])) ? $_POST['data_page'] : null;
$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $user_id);

if (!empty($user_id)) {
    $selectAll      = "SELECT m.* ";
    $selectCount    = "SELECT COUNT(m.message_id) AS count";
    $limitWhere     = "";
    $order          = " ORDER BY m.message_id DESC";
    
    $user_id    = (int)$sqlDriver->prepareData($user_id);
    $sql        = "
        FROM message AS m
            INNER JOIN rooms AS r
                ON (r.user_id = ".$user_id." OR r.user_id_friend = ".$user_id.")
                AND r.id = m.room_id
    ";
    
    if (!empty($room_id)) {
        $room_id    = (int)$sqlDriver->prepareData($room_id);
        $sql        .= " AND r.id = ".$room_id;
    }
    
    if (!empty($data_page)) {
        $data_page          = (int)$sqlDriver->prepareData($data_page);
        $countUserMessages  = null;
        $countSql           = $selectCount.$sql;
        $result             = $sqlDriver->Select($countSql);

        if (!empty($result)) {
            $countUserRooms = $result[0]['count'];
        }

        if (!empty($countUserRooms)) {
            $start1 = (MESSAGE_ON_PAGE * $data_page) - MESSAGE_ON_PAGE;
//            $start2 = ($countUserRooms > $start1) ? $start1 : ($countUserRooms - MESSAGE_ON_PAGE);
//            $start2 = (0 < $start2) ? $start2 : 0;
//            $limitWhere .= " LIMIT ".$start2.", ".MESSAGE_ON_PAGE;
            $limitWhere .= " LIMIT ".$start1.", ".MESSAGE_ON_PAGE;
        }
    }
    
    $allSql     = $selectAll.$sql.$order.$limitWhere;
    $result     = $sqlDriver->Select($allSql);
    $response   = array(
        'room_id'       => $room_id,
        'room_messages' => array(),
    );

    if (!empty($result)) {
        foreach ($result as $one) {
            $response['room_messages'][] = array(
                'message_id'        => (int)$one['message_id'],
                'message_text'      => $one['message_text'],
                'message_date'      => $one['message_date'],
                'message_author_id' => (int)$one['message_author_id'],
                'message_is_read'   => (int)$one['read'],
            ); 
        }
    }
    
    echo json_encode($response);
}
else {
    sendError(6);
}

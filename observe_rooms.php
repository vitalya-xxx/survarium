<?php
require("config.inc.php");

$user_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$last_msg_id    = isset($_REQUEST['last_message_id']) ? $_REQUEST['last_message_id'] : 0;
$sqlDriver      = new SQLDriver();

set_time_limit(0);
$counterIterations = 0;

if (!empty($user_id)) {
    $user_id            = (int)$sqlDriver->prepareData($user_id);
    $last_msg_id        = (int)$sqlDriver->prepareData($last_msg_id);
    $lastMsgIdFromDB    = 0;
    $sql                = "
        SELECT m.message_id, m.room_id
        FROM message AS m, rooms AS r
        WHERE m.`read` = 0
            AND m.room_id = r.id
            AND m.message_id > ".$last_msg_id."
            AND m.message_author_id != ".$user_id."
            AND (r.user_id = ".$user_id." OR r.user_id_friend = ".$user_id.")
        ORDER BY m.message_id DESC
        LIMIT 1
    ";

    $result             = $sqlDriver->Select($sql);
    $lastMsgIdFromDB    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
    
    if (0 === $last_msg_id) {
        $last_msg_id = $lastMsgIdFromDB;
    }

    while ($lastMsgIdFromDB <= $last_msg_id) {
        $counterIterations++;
        if ($counterIterations < LONG_POLLING_ITERATIONS) {
            sleep(SLEEP);
            clearstatcache();

            $result             = $sqlDriver->Select($sql);
            $lastMsgIdFromDB    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
        }
        else {
            exit();
        }
    }
    
    echo json_encode(array('room_id' => $result[0]['room_id']));
}
else {
    sendError(6);
}



<?php
require("test_config.php");

$user_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$last_msg_id    = isset($_REQUEST['last_message_id']) ? $_REQUEST['last_message_id'] : 0;
$room_id        = isset($_REQUEST['room_id']) ? $_REQUEST['room_id'] : null;
$currentTimeFile    = 0;
$timeLastModifaed   = 0;
$result             = array();  

set_time_limit(0);
$counterIterations = 0;

$file = getcwd().'/files/rooms/'.$room_id.'.json';
if (file_exists($file)) {
    $currentTimeFile = filemtime($file);
}

function getLastMsgIdFromFile($file, $user_id){
    $msg_id = 0;
    $msgArray = json_decode(file_get_contents($file), true);
    $msgArray = array_reverse($msgArray);

    foreach ($msgArray as $one) {
        if ($one['message']['author_id'] != $user_id) {
            $msg_id = $one['message']['msg_id'];
            break;
        }
    }
    
    return $msg_id;
}

function getLastMessageForRoom(){
    global $user_id;
    global $last_msg_id;
    global $room_id;
    
    $sqlDriver = new SQLDriver();
    $sqlDriver->connect();
    
    $user_id         = (int)$sqlDriver->prepareData($user_id);
    $last_msg_id     = (int)$sqlDriver->prepareData($last_msg_id);
    $room_id         = (int)$sqlDriver->prepareData($room_id);
    
    $sql = "
        SELECT m.message_id, m.`message_text`, m.message_date, m.message_author_id, m.`read`
        FROM message AS m
        WHERE m.`read` = 0
            AND m.room_id = ".$room_id."
            AND m.message_id > ".$last_msg_id."
            AND m.message_author_id != ".$user_id."
        ORDER BY m.message_id DESC
    ";

    $result = $sqlDriver->Select($sql);
    $sqlDriver->close();
    return $result;
}

function longPolling($timeLastModifaed, $currentTimeFile, $last_msg_id, $file){
    global $result;
    $lastMsgId = 0;

    while ($timeLastModifaed <= $currentTimeFile) {
        $counterIterations++;
        if ($counterIterations < LONG_POLLING_ITERATIONS) {
            sleep(SLEEP);
            clearstatcache();
            if (file_exists($file)) {
                $timeLastModifaed = filemtime($file);
            }
        }
        else {
            exit();
        }
    }
    
    if (file_exists($file)) {
        $lastMsgId = getLastMsgIdFromFile($file, $user_id);
    }
    else {
        $result       = getLastMessageForRoom();
        $lastMsgId    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
        $last_msg_id  = $lastMsgIdFromDB;
    }

    if ($lastMsgId <= $last_msg_id) {
        longPolling($timeLastModifaed, $currentTimeFile, $last_msg_id);
    }
    else {
        $result = getLastMessageForRoom();
    }
}

// ТОЧКА ВХОДА
if (!empty($user_id) && !empty($room_id)) {
    $lastMsgIdFromDB = 0;
    
    if (empty($last_msg_id)) {
        if (file_exists($file)) {
            $last_msg_id = getLastMsgIdFromFile($file, $user_id);
        }
        else {
            $result             = getLastMessageForRoom();
            $lastMsgIdFromDB    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
            $last_msg_id        = $lastMsgIdFromDB;
        }
    }
    
    // LONG POLLING - 2
    longPolling($timeLastModifaed, $currentTimeFile, $last_msg_id, $file);
    
    $response['room_messages'] = array();
            
    foreach ($result as $one) {
        $response['room_messages'][] = array(
            'message_id'        => (int)$one['message_id'],
            'message_text'      => $one['message_text'],
            'message_date'      => $one['message_date'],
            'message_author_id' => (int)$one['message_author_id'],
            'message_is_read'   => (int)$one['read'],
        );
    }

    echo json_encode($response);
}
else {
    sendError(6);
}

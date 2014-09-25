<?php
require("newConfig.inc.php");
require("helpers/MemcacheClass.php");
require("helpers/SQLDriverNew.php");

$user_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$last_msg_id    = isset($_REQUEST['last_message_id']) ? $_REQUEST['last_message_id'] : 0;
$room_id        = isset($_REQUEST['room_id']) ? $_REQUEST['room_id'] : null;
$result         = array();

$key = rand();
$_SESSION['user_key_message'] = $key;

set_time_limit(0);

function sendJson($responce){
//    header("Last-Modified: Tue, 21 Jul 2009 17:09:54 GMT", true);
//    header("Connection: Keep-Alive", true); //
//    header("Keep-Alive: timeout=30, max=100", true);
//    header('Content-type:application/json');
    echo json_encode($responce);
    exit();
}

/**
 * Получение последнего сообщения из MongoDB
 * @param $user_id
 * @param $room_id
 * @param int $last_msg_id
 * @return int
 */
function getLastMsgIdFromMemcache($user_id, $room_id, $last_msg_id = 0){
    global $result;

    $key    = $user_id.'_'.$room_id;
    $result = MemcacheClass::model()->getValue($key);
    return (!empty($result['message_id'])) ? $result['message_id'] : false ;
}

function getLastMessageForRoom(){
    global $user_id;
    global $last_msg_id;
    global $room_id;
    
    $user_id         = (int)SQLDriverNew::model()->prepareData($user_id);
    $last_msg_id     = (int)SQLDriverNew::model()->prepareData($last_msg_id);
    $room_id         = (int)SQLDriverNew::model()->prepareData($room_id);

    $sql = "
        SELECT m.message_id, m.`message_text`, m.message_date, m.message_author_id, m.`read`
        FROM message AS m
        WHERE m.`read` = 0
            AND m.room_id = ".$room_id."
            AND m.message_id > ".$last_msg_id."
            AND m.message_author_id != ".$user_id."
        ORDER BY m.message_id DESC
        LIMIT 1
    ";

    $result = SQLDriverNew::model()->Select($sql);
    $key    = $user_id.'_'.$room_id;
    
    $value = array(
        'message_id'        => (int)$result[0]['message_id'],
        'room_id'           => (int)$room_id,
        'message_author_id' => (int)$result[0]['message_author_id'],
        'message_text'      => $result[0]['message_text'],
        'message_date'      => $result[0]['message_date'],
        'read'              => $result[0]['read'],
    );

    if ('on' == MEMCACHE_STATE) {
        MemcacheClass::model()->setValue($key, $value);
    }
    return $result[0];
}

function longPolling($last_msg_id){
    global $result;
    global $user_id;
    global $room_id;
    global $key;
    $lastMsgId          = 0;
    $counterIterations  = 0;

    while ($lastMsgId <= $last_msg_id) {
        $counterIterations++;
        if (($counterIterations < LONG_POLLING_ITERATIONS) && ($key == $_SESSION['user_key_message'])) {
            sleep(SLEEP);
            clearstatcache();

            if ('on' == MEMCACHE_STATE) {
                $lastMsgId = getLastMsgIdFromMemcache($user_id, $room_id, $last_msg_id);
            }
            else {
                $lastMsgId = false;
            }
            
            if (false === $lastMsgId) {
                $result             = getLastMessageForRoom();
                $lastMsgIdFromDB    = !empty($result['message_id']) ? $result['message_id'] : 0;
                $lastMsgId          = $lastMsgIdFromDB;
            }
        }
        else {
            exit();
        }
    }
}

// ТОЧКА ВХОДА
if (!empty($user_id) && !empty($room_id)) {
    if (empty($last_msg_id)) {
        if ('on' == MEMCACHE_STATE) {
            $last_msg_id = getLastMsgIdFromMemcache($user_id, $room_id, $last_msg_id);
        }
        else {
            $last_msg_id = false;
        }
        
        if (false === $last_msg_id) {
            $result             = getLastMessageForRoom();
            $lastMsgIdFromDB    = !empty($result['message_id']) ? $result['message_id'] : 0;
            $last_msg_id        = $lastMsgIdFromDB;
        }
    }
    
    // LONG POLLING - 2
    longPolling($last_msg_id);

    $response['room_messages'][] = array(
        'message_id'        => (int)$result['message_id'],
        'message_text'      => $result['message_text'],
        'message_date'      => $result['message_date'],
        'message_author_id' => (int)$result['message_author_id'],
        'message_is_read'   => (int)$result['read'],
    );

    sendJson($response);
}
else {
    sendError(6);
}

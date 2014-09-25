<?php
require("../config.inc.php");

$user_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$last_msg_id    = isset($_REQUEST['last_message_id']) ? $_REQUEST['last_message_id'] : 0;
$room_id        = isset($_REQUEST['room_id']) ? $_REQUEST['room_id'] : null;
$currentTimeFile    = 0;
$timeLastModifaed   = 0;
$result             = array();
$sqlDriver          = new SQLDriver();
$con                = null;

set_time_limit(0);

/**
 * Получение последнего сообщения из MongoDB
 * @param $user_id
 * @param $room_id
 * @param int $last_msg_id
 * @return int
 */
function getLastMsgIdFromMongo($user_id, $room_id, $last_msg_id = 0){
    global $result;
    global $con;
    $dbName         = 'messages';
    $collectionName = (int)$user_id;

    $con        = new MongoClient();
    $collection = $con->{$dbName}->{$collectionName};

    $filter     = array("room_id" => (int)$room_id, 'message_id' => array('$gt' => (int)$last_msg_id));
    $message    = $collection->find($filter)->sort(array('message_id' => -1))->limit(1);

    while($document = $message->getNext()) {
        $result[0] = $document;
    }

    $collection->remove();

    if (!empty($result)) {
        return (int)$result[0]['message_id'];
    }
    else {
        return 0;
    }
}

function getLastMessageForRoom(){
    global $user_id;
    global $last_msg_id;
    global $room_id;
    global $sqlDriver;

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
    return $result;
}

function longPolling($last_msg_id){
    global $con;
    global $result;
    global $user_id;
    global $room_id;
    $lastMsgId          = 0;
    $counterIterations  = 0;

    while ($lastMsgId <= $last_msg_id) {
        $counterIterations++;
        if ($counterIterations < LONG_POLLING_ITERATIONS) {
            sleep(SLEEP);
            clearstatcache();

            $lastMsgId = getLastMsgIdFromMongo($user_id, $room_id, $last_msg_id);

//            if (empty($lastMsgId)) {
//                $result             = getLastMessageForRoom();
//                $lastMsgIdFromDB    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
//                $lastMsgId          = $lastMsgIdFromDB;
//            }
        }
        else {
            $con->close();
            exit();
        }
    }
}

// ТОЧКА ВХОДА
if (!empty($user_id) && !empty($room_id)) {
    $last_msg_id = getLastMsgIdFromMongo($user_id, $room_id, $last_msg_id);

//    if (empty($last_msg_id)) {
//        $result             = getLastMessageForRoom();
//        $lastMsgIdFromDB    = !empty($result[0]['message_id']) ? $result[0]['message_id'] : 0;
//        $last_msg_id        = $lastMsgIdFromDB;
//    }

    // LONG POLLING - 2
    longPolling($last_msg_id);
    $con->close();
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

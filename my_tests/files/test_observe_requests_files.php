<?php
require("config.inc.php");

$user_id   = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$sqlDriver = new SQLDriver();

set_time_limit(0);
$counterIterations = 0;

$incomingMessages = 0;
$incomingRequests = 0;

$currentMsgId      = 0;
$currentRequestId  = 0;

$lastMsgId      = 0;
$lastRequestId  = 0;

$currentInviteTimeFile    = 0;
$timeInviteLastModifaed   = 0;

$currentMsgTimeFile    = 0;
$timeMsgLastModifaed   = 0;

//$file = getcwd().'../files/invites/invites.json';
$inviteFile = '../files/invites/invites.json';

if (file_exists($inviteFile)) {
    $currentTimeFile = filemtime($inviteFile);
}

//$file = getcwd().'../files/invites/invites.json';
$inviteFile = '../files/rooms/'.$room_id.'.json';

if (file_exists($inviteFile)) {
    $currentTimeFile = filemtime($inviteFile);
}

/**
ID всех комнат в которых состоит пользователь 
 * 
 * @global SQLDriver $sqlDriver
 * @param type $userId
 * @return type /
 */
function getUserRoomsId($userId){
    global $sqlDriver;
    
    $sql = "
        SELECT id 
        FROM rooms
        WHERE user_id = ".$userId." OR user_id_friend = ".$userId."
    ";
    
    $result     = $sqlDriver->Select($sql);
    $idsArray   = array();
    
    foreach ($result as $one) {
        $idsArray[] = (int)$one['id'];
    }
    
    $idsStr = implode(",", $idsArray);
    return $idsStr;
}

/**
ID всех пользователей в БД
 * 
 * @global SQLDriver $sqlDriver
 * @param type $userId
 * @return type /
 */
function getUsersIds($userId){
    global $sqlDriver;
    
    $sql = "
        SELECT user_id 
        FROM users
        WHERE user_id != ".$userId."
    ";
    
    $result     = $sqlDriver->Select($sql);
    $idsArray   = array();
    
    foreach ($result as $one) {
        $idsArray[] = (int)$one['user_id'];
    }
    
    $idsStr = implode(",", $idsArray);
    return $idsStr;
}

$roomsIds = getUserRoomsId($user_id);
$usersIds = getUsersIds($user_id);

/**
Получить id последней записи
 * 
 * @global string $sqlMsg
 * @global string $sqlRequest
 * @param type $item
 * @param type $user_id
 * @return type /
 */
function getMaxId($item, $user_id){
    global $sqlDriver;
    global $roomsIds;
    global $usersIds;
    $result = array();
    
    if (!empty($roomsIds)) {
        switch ($item) {
            case 'message' : 
                $sql = "
                    SELECT MAX(m.message_id) AS maxId, COUNT(m.message_id) AS count 
                    FROM message AS m
                    WHERE m.room_id IN (".$roomsIds.")
                        AND m.message_author_id != ".$user_id."
                        AND m.`read` = 0
                ";
                break;

            case 'request' : 
                $sql = "
                    SELECT MAX(i.id) AS maxId, COUNT(i.id) AS count 
                    FROM invite AS i
                    WHERE i.user_id_friend = ".$user_id."
                        AND i.user_id IN (".$usersIds.")
                ";
                break;
        }

        $result = $sqlDriver->Select($sql);
    }
    
    return array(
        'maxId' => !empty($result[0]['maxId']) ? (int)$result[0]['maxId'] : 0,
        'count' => !empty($result[0]['count']) ? (int)$result[0]['count'] : 0,
    );
}

// ТОЧКА ВХОДА
if (!empty($user_id)) {
    $user_id = (int)$sqlDriver->prepareData($user_id);
    
    $resultMsg = getMaxId('message', $user_id);
    $resultRqs = getMaxId('request', $user_id);
    
    $currentMsgId       = $resultMsg['maxId'];
    $currentRequestId   = $resultRqs['maxId'];
    
    // LONG POLLING
    while (($lastMsgId <= $currentMsgId) && ($lastRequestId <= $currentRequestId)) {
        $counterIterations++;
        if ($counterIterations < LONG_POLLING_ITERATIONS) {
            sleep(SLEEP);
            clearstatcache();
            
            $resultMsg = getMaxId('message', $user_id);
            $resultRqs = getMaxId('request', $user_id);

            $lastMsgId     = $resultMsg['maxId'];
            $lastRequestId = $resultRqs['maxId'];
        }
        else {
            exit();
        }
    }
    
    $incomingMessages   = $resultMsg['count'];
    $incomingRequests   = $resultRqs['count'];
    
    echo json_encode(array(
        'incomingMessages' => $incomingMessages,
        'incomingRequests' => $incomingRequests,
    ));
}
else {
    sendError(6);
}




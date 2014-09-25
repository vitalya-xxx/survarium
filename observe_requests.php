<?php
require("newConfig.inc.php");
require("helpers/MemcacheClass.php");
require("helpers/SQLDriverNew.php");

set_time_limit(0);

$user_id                = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$counterIterations      = 0;
$curentCountReq         = false;
$curentCountMsg         = false;
$lastCountReq           = 0;
$lastCountMsg           = 0;
$keyFromCountRequests   = KEY_REQUEST_COUNT.$user_id;
$keyFromCountMessages   = KEY_MESSAGE_COUNT.$user_id;

function getCountFromMemcache($key){
    $result = MemcacheClass::model()->getValue($key);
    return !empty($result) ? $result['count'] : false ;
}

function sendJson($responce){
//    header("Last-Modified: Tue, 21 Jul 2009 17:09:54 GMT", true);
//    header("Connection: Keep-Alive", true); //
//    header("Keep-Alive: timeout=30, max=100", true);
//    header('Content-type:application/json');
    echo json_encode($responce);
    exit();
}

function getCountFromDB($item, $user_id, $keyMemcache){
    switch ($item) {
        case 'message' : 
            $sql = "
                SELECT COUNT(m.message_id) AS count 
                FROM message AS m
                    INNER JOIN rooms AS r
                        ON (r.user_id = ".$user_id." OR r.user_id_friend = ".$user_id.")
                        AND r.id = m.room_id
                WHERE m.message_author_id != ".$user_id."
                   AND m.`read` = 0
            ";
            break;

        case 'request' : 
            $sql = "
                SELECT COUNT(i.id) AS count
                FROM invite AS i, users AS u
                WHERE user_id_friend = ".$user_id."
                    AND u.user_id = i.user_id
            ";
            break;
    }

    $result = SQLDriverNew::model()->Select($sql);
    $count  = !empty($result[0]['count']) ? (int)$result[0]['count'] : 0;
    
    if ('on' == MEMCACHE_STATE) {
        MemcacheClass::model()->setValue($keyMemcache, array('count' => $count));
    }
    return $count;
}

// ТОЧКА ВХОДА
if (!empty($user_id)) {
    $user_id = (int)SQLDriverNew::model()->prepareData($user_id);
    
    if ('on' == MEMCACHE_STATE) {
        $curentCountMsg = getCountFromMemcache($keyFromCountMessages);
        $curentCountReq = getCountFromMemcache($keyFromCountRequests);
    }
    
    if (false === $curentCountMsg) {
        $curentCountMsg = getCountFromDB('message', $user_id, $keyFromCountMessages);
    }
    if (false === $curentCountReq) {
        $curentCountReq = getCountFromDB('request', $user_id, $keyFromCountRequests);
    }
    
    $lastCountMsg = $curentCountMsg;
    $lastCountReq = $curentCountReq;
    
    // LONG POLLING
    while (($lastCountMsg == $curentCountMsg) && ($lastCountReq == $curentCountReq)) {
        $counterIterations++;
        if ($counterIterations < LONG_POLLING_ITERATIONS) {
            sleep(SLEEP);
            clearstatcache();
            
            if ('on' == MEMCACHE_STATE) {
                $lastCountMsg = getCountFromMemcache($keyFromCountMessages);
                $lastCountReq = getCountFromMemcache($keyFromCountRequests);
            }
            else {
                $lastCountMsg = false;
                $lastCountReq = false;
            }
            
            if (false === $lastCountMsg) {
                $lastCountMsg = getCountFromDB('message', $user_id, $keyFromCountMessages);
            }
            if (false === $lastCountReq) {
                $lastCountReq = getCountFromDB('request', $user_id, $keyFromCountRequests);
            }
        }
        else {
            exit();
        }
    }
    
    sendJson(array(
        'incomingMessages' => $lastCountMsg,
        'incomingRequests' => $lastCountReq,
    ));
}
else {
    sendError(6);
}
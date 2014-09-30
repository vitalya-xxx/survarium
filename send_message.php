<?php
require("newConfig.inc.php");
require("helpers/PushWoosh.php");
require("helpers/MemcacheClass.php");
require("helpers/SQLDriverNew.php");

$user_id        = isset($_POST['id']) ? $_POST['id'] : null;
$room_id        = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$message        = isset($_POST['message']) ? $_POST['message'] : array();
$pw2            = new PushWoosh(APPLICATION_CODE, API_ACCESS);
$sqlDriverNew   = new SQLDriverNew();

$logParams = array(
    'message'    => '', 
    'method'     => 'SEND_MESSAGE', 
    'fail'       => true, 
    'mysqlError' => false, 
    'userId'     => 'from: '.$user_id.'/room: '.$room_id, 
);

/**
Запись автора и id сообщения в файл для отслеживания
 * 
 * @param type $user_id
 * @param type $room_id
 * @param type $msg_id
 * @return boolean /
 */
function writeIdInMemcache($msg_id, $object){
    global $sqlDriverNew;
    
    $sql = "
        SELECT user_id, user_id_friend
        FROM rooms
        WHERE id = ".$object['room_id']."
    ";
    
    $result         = $sqlDriverNew->Select($sql);
    $recipientId    = ($object['message_author_id'] == $result[0]['user_id']) ? $result[0]['user_id_friend'] : $result[0]['user_id'];
    $key            = $recipientId.'_'.$object['room_id'];
    
    $value = array(
        'message_id'        => (int)$msg_id,
        'room_id'           => (int)$object['room_id'],
        'message_author_id' => (int)$object['message_author_id'],
        'message_text'      => $object['message_text'],
        'message_date'      => $object['message_date'],
        'read'              => 0,
    );

    MemcacheClass::model()->setValue($key, $value);
    
    $countMemcache  = MemcacheClass::model()->getValue(KEY_MESSAGE_COUNT.$recipientId);
    $count          = !empty($countMemcache) ? $countMemcache['count'] : 0;
    MemcacheClass::model()->setValue(KEY_MESSAGE_COUNT.$recipientId, array('count' => $count + 1));
}

if (!empty($user_id) && !empty($room_id) && !empty($message)) {
    if (!empty($message['message_text']) && !empty($message['message_author_id'])) {
        $date   = date('Y-m-d H:i:s');
        $object = array(
            'message_text'          => $message['message_text'],
            'message_date'          => $date,
            'message_author_id'     => $message['message_author_id'],
            'room_id'               => $room_id,
        );

        $messageId = $sqlDriverNew->Insert('message', $object);

        if ($messageId) {
            if ('on' == MEMCACHE_STATE) {
                $logParams['message'] = 'write messageId In Memcache';
                $logParams['fail']    = false;
                writeInErroLog($logParams);
                writeIdInMemcache($messageId, $object);
            }

            $user_id = $sqlDriverNew->prepareData($user_id);
            $room_id = $sqlDriverNew->prepareData($room_id);
            
            $author     = $sqlDriverNew->Select("SELECT user_nickname FROM users WHERE user_id = ".$user_id);
            $authorName = $author[0]['user_nickname'];

            $message['message_text'] = $authorName.': '.$message['message_text'];

            $sql = "
                SELECT u.device_token 
                FROM users AS u
                    INNER JOIN rooms AS r
                        ON r.id = ".$room_id."
                        AND (r.user_id = u.user_id OR r.user_id_friend = u.user_id)
                WHERE u.user_id != ".$user_id."
            ";

            $user               = $sqlDriverNew->Select($sql);
            $deviceTokensArr    = explode(",", $user[0]['device_token']);
            $sqlDriverNew->close();

            if (!empty($deviceTokensArr)) {
                $pushes = array(
                    array(
                        'content' => $message['message_text'],
                        'devices' => $deviceTokensArr,
                        'data'    => array(
                            'custom' => "{'type':1,'user_id':".$user_id.",'room_id':".$room_id."}"
                        ),
                    ),
                );
                
                $response = $pw2->createMessage($pushes);
                
                $logParams['message'] = 'send PushWoosh'.json_encode($response);
                $logParams['fail']    = false;
                writeInErroLog($logParams);
            }
            
            echo json_encode(array(
                'message_id'    => $messageId,
                'message_date'  => $date,
            ));
        }
        else {
            $logParams['message'] = 'sendError(5)';
            writeInErroLog($logParams);
            sendError(5); 
        }
    }
    else {
        $logParams['message'] = 'sendError(6)';
        writeInErroLog($logParams);
        sendError(6);
    }
}
else {
    $logParams['message'] = 'sendError(6)';
    writeInErroLog($logParams);
    sendError(6);
}
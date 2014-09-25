<?php
require("../config.inc.php");
require("../helpers/PushWoosh.php");

$user_id    = isset($_POST['id']) ? $_POST['id'] : null;
$room_id    = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$message    = isset($_POST['message']) ? $_POST['message'] : array();

//$sqlDriver  = new SQLDriver();
$host       = "localhost";
$login      = "root";
$password   = "";
$base       = "domdot";

$mysqli = new mysqli($host, $login, $password, $base); 
$pw2    = new PushWoosh(APPLICATION_CODE, API_ACCESS);

/**
 * Добавление сообщения в MongoDB
 * @param $msg_id
 * @param $object
 */
function writeInMongoDB($msg_id, $object){
//    global $sqlDriver;
    global $mysqli;

    $sql = "
        SELECT user_id, user_id_friend
        FROM rooms
        WHERE id = ".$object['room_id']."
    ";

//    $result         = $sqlDriver->Select($sql);
//    $recipientId    = ($object['message_author_id'] == $result[0]['user_id']) ? $result[0]['user_id_friend'] : $result[0]['user_id'];

    $result = $mysqli->query($sql);

    while( $row = $result->fetch_assoc() ){ 
        $recipientId  = ($object['message_author_id'] == $row['user_id']) ? $row['user_id_friend'] : $row['user_id'];
    } 
    /* Освобождаем память */ 
    $result->close(); 
    /* Закрываем соединение */ 
    $mysqli->close();
    
    $dbName         = 'messages';
    $collectionName = $recipientId;

    $data = array(
        'message_id'        => (int)$msg_id,
        'room_id'           => (int)$object['room_id'],
        'message_author_id' => (int)$object['message_author_id'],
        'message_text'      => $object['message_text'],
        'message_date'      => $object['message_date'],
        'read'              => 0,
    );

    $con        = new MongoClient();
    $collection = $con->{$dbName}->{$collectionName};

    $collection->insert($data);
    $con->close();
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
//        $messageId = $sqlDriver->Insert('message', $object);
        
        $sql = "
            INSERT INTO `message` 
            (`message_text`, `message_date`, `message_author_id`, `room_id`)
            VALUES ('".$message['message_text']."', '".$date."', ".$message['message_author_id'].", ".$room_id.")
        ";
        $mysqli->query($sql);
        $messageId = $mysqli->insert_id; 
        
        if ($messageId) {
            writeInMongoDB($messageId, $object);
            
            echo json_encode(array(
                'message_id'    => $messageId,
                'message_date'  => $date,
            ));
            
//            $user_id = $sqlDriver->prepareData($user_id);
//            $room_id = $sqlDriver->prepareData($room_id);
//
//            $author     = $sqlDriver->Select("SELECT user_nickname FROM users WHERE user_id = ".$user_id);
//            $authorName = $author[0]['user_nickname'];
//
//            $message['message_text'] = $authorName.': '.$message['message_text'];
//
//            $sql = "
//                SELECT u.device_token
//                FROM users AS u
//                    INNER JOIN rooms AS r
//                        ON r.id = ".$room_id."
//                        AND (r.user_id = u.user_id OR r.user_id_friend = u.user_id)
//                WHERE u.user_id != ".$user_id."
//            ";
//
//            $user               = $sqlDriver->Select($sql);
//            $deviceTokensArr    = explode(",", $user[0]['device_token']);
//
//            if (!empty($deviceTokensArr)) {
//                // Сделано в цикле потому, что при передаче токенов массивом отправлялось почемуто только на первый
//                foreach ($deviceTokensArr as $token) {
//                    $pushes = array(
//                        array(
//                            'content' => $message['message_text'],
//                            'devices' => $token,
//                        ),
//                    );
//
//                    $response = $pw2->createMessage($pushes);
//                }
//
//                // Старый вариант
////                $pushes = array(
////                    array(
////                        'content' => $message['message_text'],
////                        'devices' => $deviceTokensArr,
////                    ),
////                );
////                $response = $pw2->createMessage($pushes);
//            }
        }
        else {
           sendError(5); 
        }
    }
    else {
        sendError(6);
    }
}
else {
    sendError(6);
}
<?php
require("config.inc.php");
require("helpers/PushWoosh.php");

$user_id    = isset($_POST['id']) ? $_POST['id'] : null;
$room_id    = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$message    = isset($_POST['message']) ? $_POST['message'] : array();

$sqlDriver  = new SQLDriver();
$pw2        = new PushWoosh(APPLICATION_CODE, API_ACCESS);

/**
Запись автора и id сообщения в файл для отслеживания
 * 
 * @param type $user_id
 * @param type $room_id
 * @param type $msg_id
 * @return boolean /
 */
function writeIdInFile($user_id, $room_id, $msg_id){
    $file = getcwd().'/files/rooms/'.$room_id.'.json';
    
    if (file_exists($file)) {
        $data       = ',{"message":{"author_id":'.$user_id.',"msg_id":'.$msg_id.'}}]';
        $content    = file_get_contents($file);
        $content    = substr_replace($content, $data, -1, 1);
        file_put_contents($file, $content);
    } else {
        $handle = fopen($file, 'w+');
        if (!$handle) {
            return false;
        }

        $data = '[{"message":{"author_id":'.$user_id.',"msg_id":'.$msg_id.'}}]';

        fwrite($handle, $data);
        fclose($handle);
    }
    
    return true;
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
        $messageId = $sqlDriver->Insert('message', $object);
        
        if ($messageId) {
            writeIdInFile($user_id, $room_id, $messageId);
            
            echo json_encode(array(
                'message_id'    => $messageId,
                'message_date'  => $date,
            ));
            
            $user_id = $sqlDriver->prepareData($user_id);
            $room_id = $sqlDriver->prepareData($room_id);
            
            $author     = $sqlDriver->Select("SELECT user_nickname FROM users WHERE user_id = ".$user_id);
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

            $user               = $sqlDriver->Select($sql);
            $deviceTokensArr    = explode(",", $user[0]['device_token']);

            if (!empty($deviceTokensArr)) {
                // Сделано в цикле потому, что при передаче токенов массивом отправлялось почемуто только на первый
                foreach ($deviceTokensArr as $token) {
                    $pushes = array(
                        array(
                            'content' => $message['message_text'],
                            'devices' => $token,
                        ),
                    );

                    $response = $pw2->createMessage($pushes);
                }
                
                // Старый вариант
//                $pushes = array(
//                    array(
//                        'content' => $message['message_text'],
//                        'devices' => $deviceTokensArr,
//                    ),
//                );
//                $response = $pw2->createMessage($pushes);
            }
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
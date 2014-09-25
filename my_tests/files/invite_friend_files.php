<?php
require("../config.inc.php");
require("../helpers/PushWoosh.php");

$id         = isset($_POST['id']) ? $_POST['id'] : null; 
$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : null; 
$sqlDriver  = new SQLDriver();
$pw         = new PushWoosh(APPLICATION_CODE, API_ACCESS);

/**
Проверка существования инвайта
 * 
 * @param type $user
 * @param type $friends
 * @return boolean /
 */
function rowExists($user, $friends){
    $sql = "
        SELECT COUNT(*)
        FROM `invite`
        WHERE `user_id` = ".$user." AND `user_id_friend` = ".$friends."
    ";

    $result = mysql_query($sql);

    if ($result) {
        $count = mysql_fetch_array($result);
        return (0 < $count[0]) ? true : false;
    }
    else {
        return false;
    }
}

/**
Отправка push уведомления про приглашение 
 * 
 * @param type $userId
 * @param type $friendId /
 */
function sendPushInvite($userId, $friendId){
    global $sqlDriver;
    global $pw;

    $author     = $sqlDriver->Select("SELECT user_nickname FROM users WHERE user_id = ".$userId);
    $authorName = $author[0]['user_nickname'];
    $message    = $authorName.': Invites you to be friends.';
    $sql        = "
        SELECT u.device_token 
        FROM users AS u
        WHERE u.user_id = ".$friendId."
    ";

    $user               = $sqlDriver->Select($sql);
    $deviceTokensArr    = explode(",", $user[0]['device_token']);

    if (!empty($deviceTokensArr)) {
        $pushes = array(
            array(
                'content' => $message,
                'devices' => $deviceTokensArr,
            ),
        );

        $response = $pw->createMessage($pushes);
        // можно добавить обработчик - отправился push или нет
    }
}

function writeIdInFile($user_id, $user_id_friend, $id){
//    $file = getcwd().'../files/invites/invites.json';
    $file = '../files/invites/invites.json';

    if (file_exists($file)) {
        $data       = ',{"invite":{"id":'.$id.',"$user_id":'.$user_id.',"user_id_friend":'.$user_id_friend.'}}]';
        $content    = file_get_contents($file);
        $content    = substr_replace($content, $data, -1, 1);
        file_put_contents($file, $content);
    } else {
        $handle = fopen($file, 'w+');
        if (!$handle) {
            return false;
        }

        $data = '[{"invite":{"id":'.$id.',"$user_id":'.$user_id.',"user_id_friend":'.$user_id_friend.'}}]';

        fwrite($handle, $data);
        fclose($handle);
    }
    
    return true;
}

/**
Запись приглашения в БД
 * 
 * @param type $user
 * @param type $friends /
 */
function inviteFriends($user, $friends) {
    if (false == rowExists($user, $friends)) {
        $sql = mysql_query("
            INSERT INTO `invite` (`user_id`, `user_id_friend`) 
            VALUES (".$user.", ".$friends.")
        ");
        
        if ($sql) {
            $id = mysql_insert_id();
            writeIdInFile($user, $friends, 1);
            return true;
        }
        else {
            return false;
        }
    }
    else {
        return true;
    }
}

// ТОЧКА ВХОДА
if(!empty($user_id) && !empty($id)){
    $user_id = (int)$sqlDriver->prepareData($user_id);
    $id      = (int)$sqlDriver->prepareData($id);
    
    $result = inviteFriends($id, $user_id);
    
    echo json_encode(array(
        'errorCode' => $result ? 'true' : 'false',
    ));
    
    if ($result) {
        sendPushInvite($id, $user_id);
    }
}
else {
    sendError(6);
}

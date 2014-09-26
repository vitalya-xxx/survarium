<?php
require("/../../newConfig.inc.php");
require("/../../helpers/MemcacheClass.php");
require("/../../helpers/SQLDriverNew.php");
require("/../../helpers/PushWoosh.php");

$id             = isset($_POST['id']) ? $_POST['id'] : null; 
$user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : null; 
$pw             = new PushWoosh(APPLICATION_CODE, API_ACCESS);
$sqlDriverNew   = new SQLDriverNew();

function writeCountInMemcache($user_id){
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $count          = !empty($countMemcache) ? $countMemcache['count'] : 0;
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $count + 1));
    
    return true;
}

/**
Проверка существования инвайта
 * 
 * @param type $user
 * @param type $friends
 * @return boolean /
 */
function rowExists($user, $friends){
    global $sqlDriverNew;
    
    $sql = "
        SELECT COUNT(*) AS count
        FROM `invite`
        WHERE `user_id` = ".$user." AND `user_id_friend` = ".$friends."
    ";

    $result = $sqlDriverNew->Select($sql);

    if ($result) {
        return (0 < $result[0]['count']) ? true : false;
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
    global $pw;
    global $sqlDriverNew;

    $author     = $sqlDriverNew->Select("SELECT user_nickname FROM users WHERE user_id = ".$userId);
    $authorName = $author[0]['user_nickname'];
    $message    = $authorName.': Invites you to be friends.';
    $sql        = "
        SELECT u.device_token 
        FROM users AS u
        WHERE u.user_id = ".$friendId."
    ";

    $user = $sqlDriverNew->Select($sql);
    $sqlDriverNew->close();
    $deviceTokensArr = explode(",", $user[0]['device_token']);

    if (!empty($deviceTokensArr)) {
        // Сделано в цикле потому, что при передаче токенов массивом отправлялось почемуто только на первый
//        foreach ($deviceTokensArr as $token) {
//            $pushes = array(
//                array(
//                    'content' => $message,
//                    'devices' => $token,
//                ),
//            );
//
//            $response = $pw->createMessage($pushes);
//        }
        
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

/**
Запись приглашения в БД
 * 
 * @param type $user
 * @param type $friends /
 */
function inviteFriends($user, $friends) {
    global $sqlDriverNew;
    
    if (false == rowExists($user, $friends)) {
        $data = array(
           'user_id'        => $user,  
           'user_id_friend' => $friends,  
        );
        
        $result = $sqlDriverNew->Insert('invite', $data);
        return $result ? true : false;
    }
    else {
        return true;
    }
}

// ТОЧКА ВХОДА
if(!empty($user_id) && !empty($id)){
    $user_id    = (int)$sqlDriverNew->prepareData($user_id);
    $id         = (int)$sqlDriverNew->prepareData($id);
    $result     = inviteFriends($id, $user_id);
    
    echo json_encode(array(
        'errorCode' => $result ? 'true' : 'false',
    ));
    
    if ($result) {
        if ('on' == MEMCACHE_STATE) {
            writeCountInMemcache($user_id);
        }
        sendPushInvite($id, $user_id);
    }
}
else {
    sendError(6);
}

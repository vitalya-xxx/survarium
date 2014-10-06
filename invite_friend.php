<?php
require("newConfig.inc.php");
require("helpers/MemcacheClass.php");
require("helpers/SQLDriverNew.php");
require("helpers/PushWoosh.php");

$id             = isset($_POST['id']) ? $_POST['id'] : null; 
$user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : null; 
$pw             = new PushWoosh(APPLICATION_CODE, API_ACCESS);
$sqlDriverNew   = new SQLDriverNew();

$logParams = array(
    'message'    => '', 
    'method'     => 'INVITE_FRIEND', 
    'fail'       => false, 
    'mysqlError' => false, 
    'userId'     => 'from: '.$id.'/to: '.$user_id, 
);

function writeCountInMemcache($user_id){
    global $logParams;
    
    $countMemcache  = MemcacheClass::model()->getValue(KEY_REQUEST_COUNT.$user_id);
    $count          = !empty($countMemcache) ? $countMemcache['count'] : 0;
    MemcacheClass::model()->setValue(KEY_REQUEST_COUNT.$user_id, array('count' => $count + 1));
    
    $logParams['message'] = '[2] WRITE IN THE MEMCACHE / MEM_KEY - '.KEY_REQUEST_COUNT.$user_id.' / OLD_VAL - '.$count.' / NEW_VAL - '.($count + 1);
    writeInErroLog($logParams);
    
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
    global $logParams;
    
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
        $jsonData = array(
            'type'      => 2,
            'user_id'   => $userId,
        );

        $pushes = array(
            array(
                'silentPush'        => true,
                'devices'           => $deviceTokensArr,
                'ios_root_params'   => array('aps' => array('content-available' => '1')),
                'ios_sound'         => '',
                'data'              => array('custom' => $jsonData),
            ),
            array(
                'content' => $message,
                'devices' => $deviceTokensArr,
                'data'    => array('custom' => $jsonData),
            ),
        );

        $response = $pw->createMessage($pushes);
        $logParams['message'] = '[3] send PushWoosh'.json_encode($response);
        writeInErroLog($logParams);
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
    global $logParams;
    
    if (false == rowExists($user, $friends)) {
        $data = array(
           'user_id'        => $user,  
           'user_id_friend' => $friends,  
        );
        
        $result = $sqlDriverNew->Insert('invite', $data);
        
        $logParams['message'] = '[1] ADD ROW IN DB - '.($result ? true : false);
        writeInErroLog($logParams);
        
        return $result ? true : false;
    }
    else {
        $logParams['message'] = '[1] ROW EXISTS IN DB';
        writeInErroLog($logParams);
        
        return true;
    }
}

// ТОЧКА ВХОДА
if(!empty($user_id) && !empty($id)){
    $user_id    = (int)$sqlDriverNew->prepareData($user_id);
    $id         = (int)$sqlDriverNew->prepareData($id);
    $result     = inviteFriends($id, $user_id);
    
    UpdateUserTime::model()->updateTime($id, SQLDriverNew::model());
    UpdateUserTime::model()->setStateOffOnLineAllUsers(SQLDriverNew::model());
    
    if ($result) {
        if ('on' == MEMCACHE_STATE) {
            writeCountInMemcache($user_id);
        }
        sendPushInvite($id, $user_id);
        sendSuccess();
    }
    else {
        sendError(5);
    }
}
else {
    sendError(6);
}

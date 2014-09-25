<?php
/**
Последнее сообщение для комнаты
 * 
 * @param type $roomsIds
 * @return type /
 */
function getLastMessageForRooms($room_id){
    $msg = new ArrayObject(array());
    $sql = "
        SELECT * 
        FROM message
        WHERE room_id = ".$room_id."
            ORDER BY message_date DESC   
            LIMIT 1
    ";

    $result = mysql_query($sql);
    if (mysql_num_rows($result)) {
        $messages   = mysql_fetch_assoc($result);
        $msg        = array(
            'message_id'        => (int)$messages['message_id'],
            'message_text'      => $messages['message_text'],
            'message_date'      => $messages['message_date'],
            'message_author_id' => (int)$messages['message_author_id'],
            'message_is_read'   => (int)$messages['read'],
        );
    }
    return $msg;
}

/**
Участники комнаты
 * 
 * @param type $roomsIds
 * @param type $user_id
 * @return type /
 */
function getParticipantsForRooms($room_id, $user_id, $withDeviceToken = false){
    $participants   = array();
    $sql            = "
        SELECT u.*,
        IF(r.user_id != ".$user_id.", 1, 2) as friendState
        FROM rooms AS r
            INNER JOIN users AS u
                ON (r.user_id = u.user_id OR r.user_id_friend = u.user_id)
                AND u.user_id != ".$user_id."
        WHERE r.id = ".$room_id."
    ";

    $result = mysql_query($sql);
    
    if (mysql_num_rows($result)) {
        while ($users = mysql_fetch_assoc($result)) {
            $tmpArray = array(
                'user_id'               => (int)$users['user_id'],
                'user_nickname'         => $users['user_nickname'],
                'user_icon'             => $users['user_icon'],
                'user_level'            => (int)$users['user_level'],
                'user_isOnline'         => (int)$users['user_isOnline'],
                'user_friendState'      => (int)$users['friendState'],
                'user_messages_count'   => getParticipantsMsgCount((int)$users['user_id'], $room_id),
            );
            
            if ($withDeviceToken) {
                $tmpArray['device_token'] = $users['device_token']; 
            }
            
            $participants[] = $tmpArray;
        }

        mysql_free_result($result);
    }

    return $participants;
}

/**
Кол-во сообщений от участника камнаты
 * 
 * @param type $user_id
 * @param type $rooms_ids
 * @return type /
 */
function getParticipantsMsgCount($user_id, $room_id){
    $count  = 0;
    $sql    = "
        SELECT COUNT(m.message_id)
        FROM message AS m
            INNER JOIN rooms AS r
                ON (r.user_id = ".$user_id." OR r.user_id_friend = ".$user_id.")
                AND r.id = m.room_id
        WHERE m.room_id = ".$room_id."
            AND m.message_author_id = ".$user_id."
            AND m.read = 0    
    "; 
    
    $result = mysql_query($sql);

    if ($result) {
        $count = mysql_fetch_array($result);
        $count = !empty($count[0]) ? (int)$count[0] : 0;
    }
    return $count;
}

/**
Все сообщения по выбранной комнате (лимит из conf)
 * 
 * @param type $rooms_ids
 * @return type /
 */
function getRoomMessages($room_id){
    $messages = array();
    $sql      = "
        SELECT m.message_id, m.`message_text`, m.message_date, m.message_author_id, m.`read`
        FROM message AS m
            INNER JOIN rooms AS r
                ON r.id = m.room_id
        WHERE m.room_id = ".$room_id."
            ORDER BY m.message_id DESC
            LIMIT ".ROOMS_MESSAGES_ON_PAGE."
    ";

    $result = mysql_query($sql);
    
    if (mysql_num_rows($result)) {
        while ($msg = mysql_fetch_assoc($result)) {
            $messages[] = array(
                'message_id'        => (int)$msg['message_id'], 
                'message_text'      => $msg['message_text'], 
                'message_date'      => $msg['message_date'], 
                'message_author_id' => (int)$msg['message_author_id'],
                'message_is_read'   => (int)$msg['read'],
            );
        }

        mysql_free_result($result);
    }

    return $messages;
}

/**
Проверка на существование записи в бд
 * 
 * @param type $table
 * @param type $selectId
 * @param type $where
 * @param type $operator
 * @return boolean /
 */
function isExists($table, $selectId, $where, $operator = ' AND '){
    $myWhere = '';
    
    if (is_array($where)) {
        $whereArr = array();
        
        foreach ($where as $key => $val) {
            $whereArr[] = $key." = ".$val; 
        }
        $myWhere .= implode($operator, $whereArr);
    }
    
    $sql = "
        SELECT ".$selectId."
        FROM ".$table."
        WHERE ".$myWhere."
    ";

    $result = mysql_query($sql);

    if ($result) {
        $res = mysql_fetch_array($result);
        return (int)$res[$selectId];
    }
    else {
        return false;
    }
}

/**
Проверка существования комнаты
 * 
 * @param type $user1
 * @param type $user2
 * @return boolean /
 */
function roomExists($user1, $user2){
    $sql = "
        SELECT id 
        FROM rooms 
        WHERE (user_id = ".$user1." AND user_id_friend = ".$user2.")
            OR (user_id = ".$user2." AND user_id_friend = ".$user1.")
            LIMIT 1
    ";
    
    $result = mysql_query($sql);

    if ($result) {
        $res = mysql_fetch_array($result);
        return (int)$res['id'];
    }
    else {
        return false;
    }
}

/**
Добавление новой камнаты
 * 
 * @param type $user_id
 * @param type $oponent_id
 * @return type /
 */
function createRoom($user_id, $oponent_id){
    $sql = "
        INSERT INTO rooms (user_id, user_id_friend)
        VALUES (".SQLDriver::prepareData($user_id).", ".SQLDriver::prepareData($oponent_id).")
    ";

    mysql_query($sql);
    return (int)mysql_insert_id();
}

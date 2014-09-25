<?php
require("../helpers/helpFunctionForRooms.php");

class HelpModel extends SQLDriver {
    
    public $user_id = null;

    public function __construct($user_id){
        $user_id        = $this->prepareData($user_id);
        $this->user_id  = $user_id;
    }
    
    public function getAllUsers($userId = null){
        $sqlUser = "";
        
        if (!empty($userId)) {
            $sqlUser = " WHERE u.user_id = ".$userId;
        }
        
        $sql = "
            SELECT * 
            FROM users AS u
        ";
        
        $users = $this->Select($sql.$sqlUser);
        return (!empty($userId)) ? $users[0] : $users;
    }

    public function getUserTokens(){
        $sql = "
            SELECT `device_token`
            FROM `users`
            WHERE `user_id` = ".$this->user_id."
        ";

        $user               = $this->Select($sql);
        $deviceTokensArr    = explode(",", $user[0]['device_token']);
        return $deviceTokensArr;
    }
    
    public function getIncomingMessages(){
        $sql = "
            SELECT COUNT(m.message_id) AS count, MAX(m.message_id) AS lastId
            FROM message AS m
                INNER JOIN rooms AS r
                    ON (r.user_id = ".$this->user_id." OR r.user_id_friend = ".$this->user_id.")
                    AND r.id = m.room_id
            WHERE m.`read` = 0
                AND m.message_author_id != ".$this->user_id."
        ";

        $result = $this->Select($sql);
        return array(
            'count'     => $result[0]['count'],
            'lastId'    => $result[0]['lastId'],
        );
    }
    
    public function getRequests($state){
        $sql = "";
        switch ($state) {
            case 1 :
                $sql = "
                    SELECT u.*
                    FROM invite AS i
                        INNER JOIN users AS u
                            ON u.user_id = i.user_id_friend
                    WHERE i.user_id = ".$this->user_id."
                ";
                break;
            case 2 :
                $sql = "
                    SELECT u.*, i.id
                    FROM invite AS i, users AS u
                    WHERE user_id_friend = ".$this->user_id."
                        AND u.user_id = i.user_id
                        ORDER BY i.id ASC
                ";
                break;
            case 3 :
                $sql = "
                    SELECT f.*, u.* 
                    FROM friends AS f, users AS u 
                    WHERE f.user_id = ".$this->user_id."
                        AND u.user_id = f.user_id_friend
                    GROUP BY u.user_id
                ";
                break;
        }
        
        $result = $this->Select($sql);
        $users  = array();
        
        foreach ($result as $one) {
            $users[] = array(
                'user_id'       => $one['user_id'],
                'user_nickname' => $one['user_nickname'],
            );
        }
        
        return array(
            'count'     => count($result),
            'users'     => $users,
        );
    }
    
    public function getRoomsUserIds(){
        $sql = "
            SELECT r.id
            FROM rooms AS r
            WHERE r.user_id = ".$this->user_id." OR r.user_id_friend = ".$this->user_id."
        ";
        
        $result = $this->Select($sql);
        return $result;
    }
    
    public function getUserRoomsInfo(){
        $roomsIds = $this->getRoomsUserIds();
        $rooms = array();
        
        foreach ($roomsIds as $room) {
            $room_participants = getParticipantsForRooms($room['id'], $this->user_id); 

            if (empty($room_participants)) {
                continue;
            }
            else {
                $rooms[] = array(
                    'room_id'           => $room['id'],
                    'room_last_message' => getLastMessageForRooms($room['id']),
                    'room_participants' => $room_participants,
                    'room_messages'     => getRoomMessages($room['id']),
                );
            }
        }
        
        return $rooms;
    }

    public function showArrayResult($array){
        echo '<pre>'; 
            print_r($array); 
        echo '</pre>';
    }
    
    public function View($view, $vars = array()) {
        foreach ($vars as $k => $v){
            $$k = $v;
        }

        ob_start();
        include($view);
        return ob_get_clean();
    }
}

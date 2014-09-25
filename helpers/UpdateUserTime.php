<?php

class UpdateUserTime {
    private static $model;
    public $db = false; 
    
    public static function model(){
        if (self::$model == null)
            self::$model = new UpdateUserTime();

        return self::$model;
    }

    public function updateTime($userId, $driver){
        $time = time();
        $driver->Update(
            'users', 
            array('user_isOnline' => 1, 'lastTimeUserAcive' => (int)$time), 
            'user_id = '.(int)$userId
        );
    }
    
    public function setStateOffOnLineAllUsers($driver, $userId = null){
        if (null !== $userId) {
            $this->updateTime($userId, $driver);
        }
        
        $min    = time() - TIME_USER_ACTIVITY;
        $where  = "lastTimeUserAcive < ".$min;
        $driver->Update(
            'users', 
            array('user_isOnline' => 0), 
            $where
        );
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MemcacheClass
 *
 * @author User
 */
class MemcacheClass {
    private static $model;
    public $memcache;
    public $expire;
    
    public static function model(){
        if (self::$model == null)
            self::$model = new MemcacheClass();

        return self::$model;
    }

    public function __construct(){
        $this->memcache = new Memcache();
        $this->memcache->pconnect(MEMCACHE_HOST, MEMCACHE_PORT);
        $this->expire = MEMCACHE_EXPIRE;
    }
    
    public function setValue($key, $value){
        $this->memcache->set($key, $value, 0, $this->expire); 
    }
    
    public function getValue($key){
        return $this->memcache->get($key);
    }
}

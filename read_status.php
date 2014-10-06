<?php
require("config.inc.php");
require("helpers/MemcacheClass.php");

$user_id    = (isset($_POST['id'])) ? (int)$_POST['id'] : null;
$message_id = (isset($_POST['message_id'])) ? $_POST['message_id'] : null;
$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $user_id);

function writeInMemcache($user_id, $readMsg){
    $countMemcache  = MemcacheClass::model()->getValue(KEY_MESSAGE_COUNT.$user_id);
    $count          = !empty($countMemcache) ? ($countMemcache['count'] - (int)$readMsg) : 0;
    $result         = (0 < $count) ? $count : 0;
    MemcacheClass::model()->setValue(KEY_MESSAGE_COUNT.$user_id, array('count' => $result));
}

if (!empty($message_id)) {
    $msgIdArr = array();
    
    if (is_array($message_id)) {
        $pos = strpos($message_id[0], ',');
        if ($pos === false) {
            $msgIdArr = $message_id;
        } else {
           $msgIdArr = explode(",", $message_id[0]);
        }
    }
    elseif (is_string($message_id)) {
        $msgIdArr = explode(",", $message_id);
    }

    foreach ($msgIdArr as $msgId) {
        $sqlDriver->Update('message', array('`read`' => 1), 'message_id = '.(int)$msgId);
    }
    
    $readMsg = count($msgIdArr);
    
    if ('on' == MEMCACHE_STATE) {
        writeInMemcache($user_id, $readMsg);
    }
    sendSuccess();
}
else {
    sendError(6);
}


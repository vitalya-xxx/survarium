<?php
require("newConfig.inc.php");
require("helpers/SQLDriverNew.php");

$user_id = isset($_POST['id']) ? $_POST['id'] : null;
UpdateUserTime::model()->setStateOffOnLineAllUsers(SQLDriverNew::model());

if (!empty($user_id)) {
    if (SQLDriverNew::model()->Update('users', array('device_token' => '', 'user_isOnline' => 0), 'user_id = '.$user_id)) {
        sendSuccess();
    }
    else {
        sendError(5);
    }
}
else {
    sendError(6);
}


<?php
//http://survarium/my_help/test_observes_methods.php?id=12754&last_message_id=&room_id=126

require("/../config.inc.php");
require("HelpModel.php");

$user_id        = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$last_msg_id    = isset($_REQUEST['last_message_id']) ? $_REQUEST['last_message_id'] : 0;
$room_id        = isset($_REQUEST['room_id']) ? $_REQUEST['room_id'] : null;
$helpModel      = new HelpModel($user_id);

$data = array(
    'user_id'       => $user_id,
    'last_msg_id'   => $last_msg_id,
    'room_id'       => $room_id,
);

echo $helpModel->View('view_observe_test.php', $data);

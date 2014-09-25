<?php
require("config.inc.php");

$id = isset($_POST['id']) ? (int)$_POST['id'] : null;

$incomingMessages = 0;
$incomingRequests = 0;

if (!empty($id)) {
    $sqlDriver  = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $id);
    
    $sql = "
        SELECT COUNT(m.message_id)
        FROM message AS m
            INNER JOIN rooms AS r
                ON (r.user_id = ".$id." OR r.user_id_friend = ".$id.")
                AND r.id = m.room_id
        WHERE m.`read` = 0
            AND m.message_author_id != ".$id."
    ";

    $result = mysql_query($sql);

    if ($result) {
        $count = mysql_fetch_array($result);
        $incomingMessages = (int)$count[0];
    }
    
    $sql = "
        SELECT COUNT(i.id)
        FROM invite AS i, users AS u
        WHERE user_id_friend = ".$id."
            AND u.user_id = i.user_id
    ";
    
    $result = mysql_query($sql);

    if ($result) {
        $count = mysql_fetch_array($result);
        $incomingRequests = (int)$count[0];
    }
    
    echo json_encode(array(
        'incomingMessages' => $incomingMessages,
        'incomingRequests' => $incomingRequests,
    ));
}
else {
    sendError(6);
}


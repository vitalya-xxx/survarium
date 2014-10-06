<?php
require("config.inc.php");
$news_id    = isset($_POST['news_id']) ? $_POST['news_id'] : null;
$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver);

if (!empty($news_id)) {
    $res = mysql_query("SELECT * FROM news WHERE news_id = '$news_id'", $db);
    $row = mysql_fetch_assoc($res);   

    if ($res) {
        $response['news_content'] = htmlspecialchars($row['news_content']);
        $response['news_url'] =  $row['news_url'];
    } 

    die(json_encode($response));
} else {
    sendError(6);
}

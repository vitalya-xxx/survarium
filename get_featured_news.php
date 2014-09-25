<?php
require("config.inc.php");

$user_id    = isset($_POST['id']) ? $_POST['id'] : null;
$sqlDriver  = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $user_id);

$sql = "
    SELECT *
    FROM news
        ORDER BY news_date DESC
";

$result             = $sqlDriver->Select($sql);
$response['news']   = array();

foreach ($result as $one) {
    $response['news'][] = array(
        'news_id'       => (int)$one['news_id'],
        'news_title'    => $one['news_title'],
        'news_subtitle' => $one['news_subtitle'],
        'news_icon'     => $one['news_icon'],
        'news_url'      => $one['news_url'],
        'news_date'     => $one['news_date'],
    );
}

echo json_encode($response);
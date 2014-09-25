<?php
require("config.inc.php");
$sqlDriver = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver);

$res = mysql_query("SELECT * FROM news ORDER BY news_date",$db);
$row = mysql_fetch_assoc($res);   
   
if ($res) {
    do{
        $response[] = array (	
            'news_id' =>  (int)$row['news_id'],
            'news_title' => $row['news_title'],
            'news_subtitle' => $row['news_subtitle'],
            'news_icon' => $row['news_icon'],
            'news_date' => $row['news_date'],
            'news_url' => $row['news_url']
        );
    }
    while ($row = mysql_fetch_array($res)); 

    die(json_encode($response));
}
?> 


<?php
require("config.inc.php");
$sqlDriver = new SQLDriver();
UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver);

if (!empty($_POST)) {
	 $news_id = $_POST['news_id'];
 	 if (!empty($news_id)) {
		 	    
		$res = mysql_query("SELECT * FROM news WHERE news_id = '$news_id'",$db);
        $row = mysql_fetch_array($res);   
	 
		if ($res) {
				$response['news_content'] =  $row['news_content'];
				$response['news_url'] =  $row['news_url'];
			} 		
			die(json_encode($response));
	}
	     
} else {
    sendError(6);
}

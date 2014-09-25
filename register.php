<?php


require("config.inc.php");

if (!empty($_POST)) {
    $nickname = $_POST['nickname'];
	$password =$_POST['password'];
    if (empty($nickname) || empty($password)) {
       
        $response["errorCode"] = "Empty";        
        
        die(json_encode($response));
    }
		$res = mysql_query(" SELECT * FROM users WHERE user_nickname = '$nickname'",$db);
        $row = mysql_fetch_array($res);     
   
    if ($row) {
        $response["message"] = $_POST['nickname'];
        die(json_encode($response));
    }
   
    $result = mysql_query("INSERT INTO users ( user_nickname, user_password ) VALUES ( '$nickname', '$password' )",$db);
   
   if($result){
   $res = mysql_query(" SELECT * FROM users WHERE user_nickname = '$nickname'",$db);
   $row = mysql_fetch_array($res); 

    $response["id"] = $row['user_id'];
    $sqlDriver      = new SQLDriver();
    UpdateUserTime::model()->setStateOffOnLineAllUsers($sqlDriver, $response["id"]);
    echo json_encode($response);
   }
   
    
    
} else {
?>
	<h1>Register</h1> 
	<form action="register.php" method="post"> 
	    Username:<br /> 
	    <input type="text" name="nickname" value="" /> 
	    <br /><br /> 
	    Password:<br /> 
	    <input type="password" name="password" value="" /> 
	    <br /><br /> 
	    <input type="submit" value="Register New User" /> 
	</form>
	<?php
}

?>

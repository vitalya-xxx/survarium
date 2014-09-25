<?php

require("config.inc.php");

if (!empty($_POST)) {
	$query = "INSERT INTO comments ( username, title, message ) VALUES ( :user, :title, :message ) ";

    $query_params = array(
        ':user' => $_POST['username'],
        ':title' => $_POST['title'],
		':message' => $_POST['message']
    );
  
    try {
        $stmt   = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch (PDOException $ex) {
        
        $response["success"] = 0;
        $response["message"] = "Database Error. Couldn't add post!";
        die(json_encode($response));
    }

    $response["success"] = 1;
    $response["message"] = "Username Successfully Added!";
    echo json_encode($response);
   
} else {
?>
		<h1>Add Comment</h1> 
		<form action="addcomment.php" method="post"> 
		    Username:<br /> 
		    <input type="text" name="username" placeholder="username" /> 
		    <br /><br /> 
		    Title:<br /> 
		    <input type="text" name="title" placeholder="post title" /> 
		    <br /><br />
			Message:<br /> 
		    <input type="text" name="message" placeholder="post message" /> 
		    <br /><br />
		    <input type="submit" value="Add Comment" /> 
		</form> 
	<?php
}

?> 

<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <title>Test</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>
<body>
<div class="mainDiv">
    <div class="testForm">
        <div class="titleForm">Observe requests</div>
        <form action="observe_requests.php" method="post"> 
        id:<br /> 
        <input type="text" name="id" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Observe messages</div>
        <form action="observe_messages.php" method="post">
        id:<br /> 
        <input type="text" name="id" value="" /> 
        <br />
        room_id:<br /> 
        <input type="text" name="room_id" value="" /> 
        <br />
        last_message_id:<br /> 
        <input type="text" name="last_message_id" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Send message</div>
        <form action="send_message.php" method="post"> 
        id:<br /> 
        <input type="text" name="id" value="" /> 
        <br />
        room_id:<br /> 
        <input type="text" name="room_id" value="" /> 
        <br />
        message:{message_text}:<br /> 
        <input type="text" name="message[message_text]" value="" /> 
        <br />
        message:{message_author_id}:<br /> 
        <input type="text" name="message[message_author_id]" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Read status</div>
        <form action="read_status.php" method="post">
        id:<br />
        <input type="text" name="id" value="" /> 
        <br />
        message_id[]:<br />
        <input type="text" name="message_id[]" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        </form>
        <div class="titleForm">Invite</div>
        <form action="invite_friend.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br /> 
        user_id:<br /> 
        <input type="text" name="user_id" value=""><br />
        <input class="button" type="submit" value="Go" />
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">add_friend</div>
        <form action="add_friend.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br /> 
        user_id:<br /> 
        <input type="text" name="user_id" value=""><br /> 
        <input class="button" type="submit" value="Go" /> 
         </form>
    </div>
</div>
</body>
</html>


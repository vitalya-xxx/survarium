<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <title>Test</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="mainDiv">
    <a href="/my_help/full_user_info.php">Посмотреть инфо о пользователях</a>
    <br />
    <br />
    <div class="testForm">
        <div class="titleForm">get_users_for_filter</div> 
        <form action="get_users_for_filter.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br /> 
        filter : user_nickname:<br /> 
        <input type="text" name="filter[user_nickname]" value=""><br /> 
        filter : page:<br /> 
        <input type="number" name="filter[page]" value=""><br /> 
        filter : user_friendState:<br /> 
        <input type="number" name="filter[user_friendState]" value=""><br /> 
        <input class="button" type="submit" value="Go" />
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
    
    <div class="testForm">
        <div class="titleForm">get_rooms</div>
        <form action="get_rooms.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br />
        page:<br /> 
        <input type="text" name="page" value=""><br /> 
        filter:<br /> 
        <input type="text" name="filter[user_nickname]" value=""><br />
        <input class="button" type="submit" value="Go" /> 
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">create_room</div>
        <form action="create_room.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br />
        user_id:<br /> 
        <input type="text" name="user_id" value=""><br /> 
        <input class="button" type="submit" value="Go" /> 
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">Get News</div>
        <form action="get_news.php" method="post" >
        news_id:<br /> 
        <input type="text" name="news_id" value=""><br /> 
        <input class="button" type="submit" value="Go" />
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">Get News Details</div>
        <form action="get_news_details.php" method="post" >
        news_id:<br /> 
        <input type="text" name="news_id" value=""><br /> 
        <input class="button" type="submit" value="Go" />
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">Get User Data</div>
        <form action="get_user_data.php" method="post" >
        user_id:<br /> 
        <input type="text" name="id" value=""><br /> 
        <input class="button" type="submit" value="Go" />
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">Reply friend</div>
        <form action="reply_friend.php" method="post" >
        id:<br /> 
        <input type="text" name="id" value=""><br />
        user_id:<br /> 
        <input type="text" name="user_id" value=""><br />
        reply:<br /> 
        <input type="text" name="reply" value=""><br />
        <input class="button" type="submit" value="Go" />
        </form>
    </div>
    
    <div class="testForm">
        <div class="titleForm">Signup</div>
        <form action="signup.php" method="post" >
        user_email:<br /> 
        <input type="text" name="user_email" value=""><br />
        <input class="button" type="submit" value="Go" />
        </form>
    </div>

    <div class="testForm">
        <div class="titleForm">Edit login data</div>
        <form action="edit_login_data.php" method="post" >
        user_nickname:<br /> 
        <input type="text" name="user_nickname" value=""><br />
        user_password:<br /> 
        <input type="text" name="user_password" value=""><br />
        id:<br /> 
        <input type="text" name="id" value=""><br />
        <input class="button" type="submit" value="Go" />
        </form>
    </div>

    <div class="testForm">
        <div class="titleForm">Login</div>
        <form action="login.php" method="post"> 
        user_nickname:<br /> 
        <input type="text" name="user_nickname" value="" /> 
        <br />
        user_password:<br /> 
        <input type="password" name="user_password" value="" /> 
        <br />
        <input class="button" type="submit" value="Login" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Observe rooms</div>
        <form action="observe_rooms.php" method="post"> 
        id:<br /> 
        <input type="text" name="id" value="" /> 
        <br />
        last_message_id:<br /> 
        <input type="text" name="last_message_id" value="" /> 
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
        <div class="titleForm">Observe requests</div>
        <form action="observe_requests.php" method="post"> 
        id:<br /> 
        <input type="text" name="id" value="" /> 
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
        message:{message_date}:<br /> 
        <input type="text" name="message[message_date]" value="<?php echo time();?>" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Get featured news</div>
        <form action="get_featured_news.php" method="post"> 
        id:<br />
        <input type="text" name="id" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Send token</div>
        <form action="send_token.php" method="post"> 
        id:<br />
        <input type="text" name="id" value="" /> 
        <br />
        user_token:<br />
        <input type="text" name="user_token" value="" /> 
        <br />
        user_token_old:<br />
        <input type="text" name="user_token_old" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Test send push message</div>
        <form action="test_send_push_message.php" method="post">
        user_token:<br />
        <input type="text" name="user_token" value="" /> 
        <br />
        message:<br />
        <input type="text" name="message" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
    
    <div class="testForm">
        <div class="titleForm">Get message history</div>
        <form action="get_message_history.php" method="post">
        id:<br />
        <input type="text" name="id" value="" /> 
        <br />
        room_id:<br />
        <input type="text" name="room_id" value="" /> 
        <br />
        data_page:<br />
        <input type="text" name="data_page" value="" /> 
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
        <div class="titleForm">Logout</div>
        <form action="logout.php" method="post">
        id:<br />
        <input type="text" name="id" value="" /> 
        <br />
        <input class="button" type="submit" value="Go" /> 
        </form> 
    </div>
</div>
</body>
</html>


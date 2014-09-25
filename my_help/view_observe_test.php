<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <title>Инфо о пользователе:</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script type='text/javascript' src="js/script.js"></script>
    <link rel="stylesheet" href="/css/style.css" />
</head>
<body>
<div class="mainDiv">
    <input type="hidden" id="user_id" value="<?php echo $user_id?>"/>
    <input type="hidden" id="last_msg_id" value="<?php echo $last_msg_id?>"/>
    <input type="hidden" id="room_id" value="<?php echo $room_id?>"/>
    <input type="button" id="startObserve" value="Старт"/>
    <input type="button" id="stopObserve" value="Стоп"/>
    <br />
    <div class="requestBlock">
        <div class="statistic" id="requestStart">
            <div class="title">Запуск observe_request</div>
            <div class="text"></div>
        </div>
        <div class="statistic" id="requestResult">
            <div class="title">Ответ observe_request</div>
            <div class="text"></div>
        </div>
    </div>
    <div class="messagesBlock">
        <div class="statistic" id="messagesStart">
            <div class="title">Запуск observe_messages</div>
            <div class="text"></div>
        </div>
        <div class="statistic" id="messagesResult">
            <div class="title">Ответ observe_messages</div>
            <div class="text"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>
</body>
</html>

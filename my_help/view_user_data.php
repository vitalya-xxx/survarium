<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <title>Инфо о пользователе:</title>
    <link rel="stylesheet" href="/css/style.css" />
</head>
<body>
<div class="mainDiv">
    <a href="/test.php">API</a>
    <div class="listUsers">
        <?php if ($userId) :?>
            <div class="userData">
                <div class="avatar">
                    <img src="/<?php echo $userInfo['user_icon']?>" />
                </div>
                <div class="userInfo">
                    <table class="nestedTable">
                        <tr>
                            <td>id пользователя</td>
                            <td><?php echo $userInfo['user_id']?></td>
                        </tr>
                        <tr>
                            <td>Логин</td>
                            <td><?php echo $userInfo['user_nickname']?></td>
                        </tr>
                        <tr>
                            <td>Пароль</td>
                            <td><?php echo $userInfo['user_password']?></td>
                        </tr>
                        <tr>
                            <td>Уровень</td>
                            <td><?php echo $userInfo['user_level']?></td>
                        </tr>
                        <tr>
                            <td>Онлайн</td>
                            <td><?php echo $userInfo['user_isOnline']?></td>
                        </tr>
                    </table>
                </div>
                <div class="clear"></div>
            </div>
        <?php else :?>
            <div class="titleListUsers">Выберите пользователя</div>
        <?php endif;?>
        <?php foreach ($allUsers as $user):?>
            <a href="/my_help/full_user_info.php/?id=<?php echo $user['user_id']?>"><?php echo $user['user_id'].' (<span>'.$user['user_nickname'].'</span>)'?></a>,&nbsp; 
        <?php endforeach;?>
    </div>
    <?php if ($userId) :?>
        <div class="dataTable">
            <div class="titleTable">Не прочитанные сообщения (все комнаты)</div>
            <div class="table">
                <table>
                    <th>Кол-во</th>
                    <th>ID последнего</th>
                    <tr>
                        <td><?php echo $incomingMessages['count']?></td>
                        <td><?php echo $incomingMessages['lastId']?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="dataTable">
            <div class="titleTable">Пользователя пригласили:</div>
            <div class="table">
                <table>
                    <th>Кол-во</th>
                    <th>ID последнего пригласившего</th>
                    <tr>
                        <td><?php echo $incomingRequests['count']?></td>
                        <td>
                            <?php foreach ($incomingRequests['users'] as $user):?>
                                <a href="/my_help/full_user_info.php/?id=<?php echo $user['user_id']?>"><?php echo $user['user_id'].' (<span>'.$user['user_nickname'].'</span>)'?></a>,&nbsp;
                            <?php endforeach;?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    
        <div class="dataTable">
            <div class="titleTable">Пользователь пригласил:</div>
            <div class="table">
                <table>
                    <th>Кол-во</th>
                    <th>ID последнего пригласившего</th>
                    <tr>
                        <td><?php echo $heInvited['count']?></td>
                        <td>
                            <?php foreach ($heInvited['users'] as $user):?>
                                <a href="/my_help/full_user_info.php/?id=<?php echo $user['user_id']?>"><?php echo $user['user_id'].' (<span>'.$user['user_nickname'].'</span>)'?></a>,&nbsp;
                            <?php endforeach;?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    
        <div class="dataTable">
            <div class="titleTable">Пользователь дружит с:</div>
            <div class="table">
                <table>
                    <th>Кол-во</th>
                    <th>ID последнего пригласившего</th>
                    <tr>
                        <td><?php echo $friends['count']?></td>
                        <td>
                            <?php foreach ($friends['users'] as $user):?>
                                <a href="/my_help/full_user_info.php/?id=<?php echo $user['user_id']?>"><?php echo $user['user_id'].' (<span>'.$user['user_nickname'].'</span>)'?></a>,&nbsp;
                            <?php endforeach;?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="dataTable">
            <div class="titleTable">Токены пользователя</div>
            <div class="table">
                <table class="mainTable">
                    <th>№</th>
                    <th>Токен</th>
                    <?php foreach ($userTokens as $key => $token) :?>
                        <tr>
                            <td><?php echo $key + 1?></td>
                            <td><div class="token"><?php echo $token?></div></td>
                        </tr>
                    <?php endforeach;?>
                </table>
            </div>
        </div>

        <div class="dataTable">
            <div class="titleTable">Комнаты в которых состоит пользователь</div>
            <div class="table">
                <table class="mainTable">
                    <th>ID комнаты</th>
                    <th>Последнее сообщение</th>
                    <th>Участники</th>
                    <th>Сообщения комнаты</th>
                    <?php foreach ($rooms as $one) :?>
                        <tr>
                            <td><?php echo $one['room_id']?></td>
                            <td>
                                <table class="nestedTable">
                                    <tr>
                                        <td>id сообщения</td>
                                        <td><?php echo $one['room_last_message']['message_id']?></td>
                                    </tr>
                                    <tr>
                                        <td>Текст</td>
                                        <td><?php echo $one['room_last_message']['message_text']?></td>
                                    </tr>
                                    <tr>
                                        <td>Дата</td>
                                        <td><?php echo $one['room_last_message']['message_date']?></td>
                                    </tr>
                                    <tr>
                                        <td>id автора</td>
                                        <td><?php echo $one['room_last_message']['message_author_id']?></td>
                                    </tr>
                                    <tr>
                                        <td>Прочитано</td>
                                        <td><?php echo $one['room_last_message']['message_is_read']?></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <?php foreach ($one['room_participants'] as $participant) :?>
                                    <table class="nestedTable">
                                        <tr>
                                            <td>id пользователя</td>
                                            <td><?php echo $participant['user_id']?></td>
                                        </tr>
                                        <tr>
                                            <td>Логин</td>
                                            <td><a href="/my_help/full_user_info.php/?id=<?php echo $participant['user_id']?>"><?php echo $participant['user_nickname']?></a></td>
                                        </tr>
                                        <tr>
                                            <td>Аватар</td>
                                            <td><img src="/<?php echo $participant['user_icon']?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>Уровень</td>
                                            <td><?php echo $participant['user_level']?></td>
                                        </tr>
                                        <tr>
                                            <td>Онлайн</td>
                                            <td><?php echo $participant['user_isOnline']?></td>
                                        </tr>
                                        <tr>
                                            <td>Friend state</td>
                                            <td><?php echo $participant['user_friendState']?></td>
                                        </tr>
                                        <tr>
                                            <td>Кол-во сообщений от пользователя</td>
                                            <td><?php echo $participant['user_messages_count']?></td>
                                        </tr>
                                    </table>
                                <?php endforeach;?>                            
                            </td>
                            <td>
                                <div class="messagesContainer">
                                    <?php foreach ($one['room_messages'] as $msg) :?>
                                    <div class="msg">
                                        <div class="info">
                                            <div class="block1">
                                                <div class="key">ID сообщения</div>
                                                <div class="val"><?php echo $msg['message_id']?></div>
                                            </div>
                                            <div class="block2">
                                                <div class="key">ID автора</div>
                                                <div class="val"><?php echo $msg['message_author_id']?></div>
                                            </div>
                                            <div class="block2">
                                                <div class="key <?php echo ($msg['message_is_read']) ? 'view' : 'hide';?>">Прочитано</div>
                                                <div class="val"><?php echo $msg['message_is_read']?></div>
                                            </div>
                                            <div class="block1">
                                                <div class="key">Дата</div>
                                                <div class="val"><?php echo $msg['message_date']?></div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="text <?php echo ($userId == $msg['message_author_id']) ? 'curentUser' : 'oponentUser';?>"><?php echo $msg['message_text']?></div>
                                        <div class="info">
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </td>
                        </tr>    
                    <?php endforeach;?>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>
</body>
</html>

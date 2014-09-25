<?php
echo 'Test_memcache';
echo '<br />';

$memcache = new Memcache();
//$memcache->pconnect('91.206.200.51', 11211);
//$memcache->pconnect('http://t.bnb.in.ua/', 11211);
$memcache->pconnect('127.0.0.1', 11211);
$key = 12754;
$value = array(
    'msg_id'    => 124582,
    'room_id'   => 127,
    'author_id' => 12753,
    'text'      => 'Hello!',
    'date'      => date('Y-m-d H:m:i'),
);

$expire = 600;
//$memcache->set($key, $value, 0, $expire); // Сохраняем значение на 10 минут
$memcache->set($key, $value, 0, $expire); // Сохраняем значение на 10 минут
$result = $memcache->get(12754);
echo '<pre>'; print_r($result); echo '</pre>';
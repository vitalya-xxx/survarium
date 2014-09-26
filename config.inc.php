<?php
require("helpers/SQLDriver.php");
require("helpers/UpdateUserTime.php");

/*$host     = "localhost";
$login      = "domdot_sur";
$password   = "d2y9h4ya";
$db         = mysql_connect ($host, $login ,$password );
$base       = "survarium";*/

//$host       = "localhost";
//$login      = "root";
//$password   = "";
//$base       = "domdot";

$host       = "domdot.mysql.ukraine.com.ua";
$login      = "domdot_sur";
$password   = "d2y9h4ya";
$base       = "domdot_sur";

$db = mysql_connect ($host, $login ,$password );
mysql_select_db($base,$db);
mysql_query('SET CHARACTER SET utf8');

session_start(); 

define('ROOMS_ON_PAGE', 10);
define('MESSAGE_ON_PAGE', 30);
define('ROOMS_MESSAGES_ON_PAGE', 30);

define('LONG_POLLING_ITERATIONS', 10);
define('SLEEP', 1);

define('APPLICATION_CODE', '2DBFA-25029');
define('API_ACCESS', 'gt+bvimITe8eJxt/Qzrs9qr/W9kdT+grrfAfitdMS1ks5hhWb2ZGjY5hDdKc+chG+SQgF3MIQiH5YqvQrvYq');

//define('MEMCACHE_HOST', 'unix:///home/domdot/.system/memcache/socket');
//define('MEMCACHE_PORT', 0);

define('MEMCACHE_HOST', '127.0.0.1');
define('MEMCACHE_PORT', 11211);

define('MEMCACHE_EXPIRE', 18000);
define('KEY_REQUEST_COUNT', 'countReqFor_');
define('KEY_MESSAGE_COUNT', 'countMsgFor_');
define('MEMCACHE_STATE', 'on');

define('TIME_USER_ACTIVITY', 600);

/**
Отправка сообщения об ошибке
 * 
 * @param type $errorCode /
 */
function sendError($errorCode){
    echo json_encode(array(
        'errorCode' => $errorCode,
    ));
    die();
}

function writeInErroLog($params){
    $date       = date('d.m.Y H:i:s');
    $type       = $params['fail'] ? 'ERROR' : 'SUCCESS';
    $mysql      = $params['mysqlError'] ? ' - MYSQL' : '';
    $message    = $type.$mysql." / ".$date. " / method: ".$params['method']." / userId: ".$params['userId']." - [".$params['message']."] \n";
    error_log($message, 3, "log/php_errors.log");
}
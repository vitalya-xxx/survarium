<?php
require("helpers/SQLDriver.php");

/*$host     = "localhost";
$login      = "domdot_sur";
$password   = "d2y9h4ya";
$db         = mysql_connect ($host, $login ,$password );
$base       = "survarium";*/

define('ROOMS_ON_PAGE', 10);
define('MESSAGE_ON_PAGE', 30);
define('ROOMS_MESSAGES_ON_PAGE', 30);

define('LONG_POLLING_ITERATIONS', 10);
define('SLEEP', 1);

define('APPLICATION_CODE', '2DBFA-25029');
define('API_ACCESS', 'gt+bvimITe8eJxt/Qzrs9qr/W9kdT+grrfAfitdMS1ks5hhWb2ZGjY5hDdKc+chG+SQgF3MIQiH5YqvQrvYq');

/**
Отправка сообщения об ошибке
 * 
 * @param type $errorCode /
 */
function sendError($errorCode){
    echo json_encode(array(
        'errorCode' => $errorCode,
    ));
}
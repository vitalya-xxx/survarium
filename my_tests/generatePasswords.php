<?php
$password = '1234567';

// Пианинка
$solt = 'D6U7XTGoS8DfAM';
$mask = md5(md5($password).$solt);
//
//// Моджонг
//$solt = 'This_is+The\best--sa1t__eVer';
//$mask = md5(md5($password).$solt);

// Yii
//$solt = 'sdhre3463457*^dfghmghkj,%%&$';
//$mask = md5($password.$solt);

echo $mask;



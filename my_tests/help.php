<?php
//$addFlag = '+';
//$result  = 5;
//
//$levelTime      = 20;
//$StarTwoTime    = 15;
//$StarThreeTime  = 4;
//
//$expressions = array(
//    '$levelTime'        => $levelTime.$addFlag.$result,
//    '$StarTwoTime'      => $StarTwoTime.$addFlag.'('.$result.' / 2)',
//    '$StarThreeTime'    => $StarThreeTime.$addFlag.'('.$result.' / 2)',
//);
//
//foreach ($expressions as $key => $val) {
//    echo($key." = ".$val.";");
//    eval($key." = ".$val.";");
//}
//
//var_dump($levelTime);
//var_dump($StarTwoTime);
//var_dump($StarThreeTime);
//-------------------------------------
//$operator = ' + ';
//$levelTime = '5';
//$result = '3';
//
//$str = $levelTime.$operator.$result;
//$str1 = '5+3';
//
//var_dump($str);
//eval("\$str = ".$str.";");
//var_dump($str);
//-----------------------------------


//$str = "[1754,1756]";
//$arr = array(1754,1756);
//
//var_dump($str);
//var_dump($arr);
//
//-----------------------

//$percent = 9.23529411765;
//$counter = (int)($percent / 10);
//echo $counter.'<br />';
//$result  = $counter * 10;
//
//echo $result;

//$time = 1410441301;
//$date = date('d.m.Y H:i:s', $time);
//echo $date;
//echo '<br />';
//$time = 1410440401;
//$date = date('d.m.Y H:i:s', $time);
//echo $date;

$password 	= '1234567';
		$solt 		= 'D6U7XTGoS8DfAM';
		$mask 		= md5(md5($password).$solt);
		echo $mask;	
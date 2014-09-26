<?php
require("config.inc.php");

$email  = isset($_POST['user_email']) ? $_POST['user_email'] : null;
$sql    = new SQLDriver();

$logParams = array(
    'message'    => '', 
    'method'     => 'SIGNUP', 
    'fail'       => true, 
    'mysqlError' => false, 
    'userId'     => $email, 
);

/**
Проверка корректности email
 * 
 * @param type $email
 * @return type /
 */
function validateEmail($email){
    return preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/", $email);
}

/**
 * Верификация почтового ящика на существование
 * @param $email
 * @return bool
 */
function verificationEmail($email){
    $email_arr  = explode("@" , $email);
    $host       = $email_arr[1];
    $hostExist  = true;

    if (!getmxrr($host, $mxhostsarr)) {
        $hostExist = false;
    }

    return $hostExist;
}

/**
* Генерация пароля для пользователя
* @param int $length
* @return string
*/
function generatePassword($length = 10){
   $chars  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
   $code   = "";
   $clen   = strlen($chars) - 1;

   while (strlen($code) < $length)
       $code .= $chars[mt_rand(0, $clen)];

   return $code;
}

/**
Генерация никнейма для пользователя
 * 
 * @param type $email
 * @return string /
 */
function generateNickname($email){
    global $sql;
    $email_arr  = explode("@" , $email);
    $nick       = $email_arr[0];
    
    while ($sql->rowExists('users', 'user_nickname', $nick)) {
        $suffix = generatePassword(3);
        $nick   = $nick.'_'.$suffix;
    }
    
    return $nick;
}

if (!empty($email)) {
    // Валидация email
    if (!validateEmail($email)) {
        $logParams['message'] = 'sendError(1)';
        writeInErroLog($logParams);
        sendError(1);
    }
    else {
        // Проверка существования email
        if (!verificationEmail($email)) {
            $logParams['message'] = 'sendError(2)';
            writeInErroLog($logParams);
            sendError(2);
        }
        else {
            // Проверка наличия email в БД
            $query = "
                SELECT *
                FROM users
                WHERE user_email = '".$email."'
            ";
            
            $rowExists = $sql->Select($query);
            
            if (empty($rowExists)) {
                $response = array(
                    'user_nickname' => generateNickname($email),
                    'user_password' => generatePassword(),
                );
                
                $object = array(
                    'user_nickname' => $response['user_nickname'],
                    'user_password' => $response['user_password'],
                    'user_email'    => $email,
                );
                
                // Регистрация пользователя
                $result = $sql->Insert('users', $object);
                
                if ($result) {
                    $response['id'] = (int)$result;
                    
                    $logParams['message']   = 'user add in DB / '.json_encode($response);
                    $logParams['fail']      = false;
                    $logParams['userId']    = $email.'/'.$response['id'];
                    writeInErroLog($logParams);
                    
                    echo json_encode($response);
                }
                else {
                    $logParams['message'] = 'sendError(5)';
                    writeInErroLog($logParams);
                    sendError(5);
                }
            }
            else {
                if (1 == $rowExists[0]['confirm']) {
                    $logParams['message'] = 'sendError(3)';
                    writeInErroLog($logParams);
                    sendError(3);
                }
                else {
                    $response = array(
                        'user_nickname' => $rowExists[0]['user_nickname'],
                        'user_password' => $rowExists[0]['user_password'],
                        'id'            => $rowExists[0]['user_id'],
                    );
                    
                    $logParams['message']   = 'record in the database but is not confirmed / '.json_encode($response);
                    $logParams['fail']      = false;
                    $logParams['userId']    = $email.'/'.$response['id'];
                    writeInErroLog($logParams);
                    
                    UpdateUserTime::model()->setStateOffOnLineAllUsers($sql, $response['id']);
                    echo json_encode($response);
                }
            }
        }
    }
}
else {
    $logParams['message'] = 'sendError(6)';
    writeInErroLog($logParams);
    sendError(6);
}


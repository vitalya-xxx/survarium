<?php
require("newConfig.inc.php");
require("helpers/SQLDriverNew.php");

$user_id    = isset($_POST['id']) ? $_POST['id'] : null;
$filter     = isset($_POST['filter']) ? $_POST['filter'] : null;
$response   = array();

UpdateUserTime::model()->setStateOffOnLineAllUsers(SQLDriverNew::model(), $user_id);

$start          = 0;
$whereNickName  = "";
$limitWhere     = " LIMIT ".USERS_ON_PAGE." ";
$order          = "ORDER BY u.user_nickname";

$sqlState_0 = "
    SELECT u.*
    FROM users AS u
    WHERE u.user_id != ".$user_id."
        AND u.user_id NOT IN (
            SELECT u1.user_id 
            FROM friends AS f1, users AS u1 
            WHERE f1.user_id = ".$user_id."
                AND u1.user_id = f1.user_id_friend 
        )
        AND u.user_id NOT IN (
            SELECT u2.user_id
            FROM invite AS i2, users AS u2
            WHERE (i2.user_id = ".$user_id." AND u2.user_id = i2.user_id_friend)
                OR (i2.user_id_friend = ".$user_id." AND u2.user_id = i2.user_id)
        )
";

$sqlState_1 = "
    SELECT u.*
    FROM invite AS i
        INNER JOIN users AS u
            ON u.user_id = i.user_id_friend
    WHERE i.user_id = ".$user_id."
";

$sqlState_2 = "
    SELECT u.*
    FROM invite AS i, users AS u
    WHERE user_id_friend = ".$user_id."
        AND u.user_id = i.user_id
";

$sqlState_3 = "
    SELECT f.*, u.* 
    FROM friends AS f, users AS u 
    WHERE f.user_id = ".$user_id."
        AND u.user_id = f.user_id_friend
";

$groupByUserId = " GROUP BY u.user_id ";

if (!empty($user_id)) {
    if ('' != $filter['user_nickname']) {
        $whereNickName = " AND u.user_nickname LIKE '".$filter['user_nickname']."%' ";
    }

    if ('' != $filter['page']) {
        $start      = getStartForPagination($filter['page']);
        $limitWhere = " LIMIT ".$start.", ".USERS_ON_PAGE." ";
    }
    
    if ('' == $filter['user_friendState']) {
        if (checkUserFriends($user_id)) {
            $fullSql    = $sqlState_3.$whereNickName.$groupByUserId.$order;
            $result     = SQLDriverNew::model()->Select($fullSql);
            addDataToResponce($result, 3);
            
            $count = count($response);
            uasort($response, 'compare_lastname');

            if ('' != $filter['user_nickname'] && USERS_ON_PAGE > $count) {
                $fullSql    = $sqlState_0.$whereNickName.$order;
                $result     = SQLDriverNew::model()->Select($fullSql);
                $array_0    = addDataToResponce($result, 0, true);

                $fullSql    = $sqlState_1.$whereNickName.$order;
                $result     = SQLDriverNew::model()->Select($fullSql);
                $array_1    = addDataToResponce($result, 1, true);
                
                uasort($array_0, 'compare_lastname');
                uasort($array_1, 'compare_lastname');

                foreach ($array_1  as $one) {
                    array_push($response, $one);
                }
                
                foreach ($array_0  as $one) {
                    array_push($response, $one);
                }
            }
            
            $response = array_slice($response, $start, USERS_ON_PAGE);
        }
        else {
            getOtherUsers();
        }
    }
    elseif ('' != $filter['user_friendState']) {
        $state = (int)$filter['user_friendState'];
        switch ($state) {
            case 0 :
                $fullSql    = $sqlState_0.$whereNickName.$order.$limitWhere;
                $result     = SQLDriverNew::model()->Select($fullSql);
                addDataToResponce($result, 0);
                break;
            case 1 :
                $fullSql    = $sqlState_1.$whereNickName.$order.$limitWhere;
                $result     = SQLDriverNew::model()->Select($fullSql);
                addDataToResponce($result, 1);
                break;
            case 2 :
                $fullSql    = $sqlState_2.$whereNickName.$order.$limitWhere;
                $result     = SQLDriverNew::model()->Select($fullSql);
                addDataToResponce($result, 2);
                break;
            case 3 :
                $fullSql    = $sqlState_3.$whereNickName.$groupByUserId.$order.$limitWhere;
                $result     = SQLDriverNew::model()->Select($fullSql);
                addDataToResponce($result, 3);
                break;
        }
    }
    
    echo json_encode($response);
}
else {
    sendError(6);
}

function getStartForPagination($_page){
    $page  = (int)$_page;
    $start = (USERS_ON_PAGE * $page) - USERS_ON_PAGE;
    $start = (0 < $start) ? $start : 0;
    $start = (0 < $page) ? $start + USERS_ON_PAGE : $start;
    return $start;
}

/**
Проверка существования у пользователя друзей
 * 
 * @param type $user_id
 * @return type /
 */
function checkUserFriends($user_id){
    $sql = "
        SELECT COUNT(friends.id) AS count 
        FROM friends, users 
        WHERE friends.user_id = ".$user_id."
            AND users.user_id = friends.user_id_friend
    ";
    $result = SQLDriverNew::model()->Select($sql);
    return (0 < $result[0]['count']) ? true : false;
}

function addDataToResponce($result, $friendState, $return = false){
    global $response;
    $localArray = array();
    
    foreach ($result as $row) {
        $localArray[] = array(
            'user_id'           =>  (int)$row['user_id'],
            'user_nickname'     => $row['user_nickname'],
            'user_icon'         => $row['user_icon'],
            'user_level'        => (int)$row['user_level'],
            'user_isOnline'     => (int)$row['user_isOnline'],
            'user_friendState'  => $friendState
        );
    }
    
    if ($return) {
        return $localArray;
    }
    else {
        foreach ($localArray  as $one) {
            array_push($response, $one);
        }
    }
}

function getOtherUsers(){
    global $response;
    global $sqlState_0;
    global $sqlState_1;
    global $whereNickName;
    global $order;
    global $start;
    
    $fullSql    = $sqlState_0.$whereNickName.$order;
    $result     = SQLDriverNew::model()->Select($fullSql);
    addDataToResponce($result, 0);

    $fullSql    = $sqlState_1.$whereNickName.$order;
    $result     = SQLDriverNew::model()->Select($fullSql);
    addDataToResponce($result, 1);

    uasort($response, 'compare_lastname');
    $response = array_slice($response, $start, USERS_ON_PAGE);
}

function compare_lastname($a, $b) {
  return strnatcmp(strtolower($a['user_nickname']), strtolower($b['user_nickname']));
}

  

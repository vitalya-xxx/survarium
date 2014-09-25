<?php
require("../config.inc.php");
require("HelpModel.php");

$user_id    = isset($_GET['id']) ? $_GET['id'] : null;
$helpModel  = new HelpModel($user_id);

$resul1 = array();
$resul2 = array();

if (!empty($user_id)) {
    $userInfo           = $helpModel->getAllUsers($user_id);
    $userTokens         = $helpModel->getUserTokens();
    $incomingMessages   = $helpModel->getIncomingMessages();
    $incomingRequests   = $helpModel->getRequests(2);
    $heInvited          = $helpModel->getRequests(1);
    $friends            = $helpModel->getRequests(3);
    $rooms              = $helpModel->getUserRoomsInfo();
    
    $result1 = array(
        'incomingMessages'  => $incomingMessages,
        'incomingRequests'  => $incomingRequests,
        'heInvited'         => $heInvited,
        'friends'           => $friends,
        'rooms'             => $rooms,
        'userTokens'        => $userTokens,
        'userId'            => $user_id,
        'allUsers'          => $allUsers,
        'userInfo'          => $userInfo,
    );
}
else {
    $result1 = array('userId' => false);
}

$allUsers = $helpModel->getAllUsers();


$result2 = array(
    'allUsers' => $allUsers,
);

$result = array_merge($result1, $result2);

//$helpModel->showArrayResult($result);
echo $helpModel->View('view_user_data.php', $result);



<?php

//$dbName         = 'messages';
//$collectionName = 12754;
//
//$con        = new MongoClient();
//$collection = $con->$dbName->$collectionName;
//
//
//$filter     = array("room_id" => 126, 'message_id' => array('$gt' => 0));
//$message    = $collection->find($filter)->sort(array('message_id' => -1));
////$message    = $collection->find($filter)->sort(array('message_id' => -1));
////$message    = $collection->find()->sort(array('message_id' => -1));
//
//while($document = $message->getNext()) {
//    $result[] = $document;
//}
//var_dump($result[0]['message_id']);
//echo '<pre>'; print_r($result); echo '</pre>'; die();


// ---------------------------------------------
$data = array(
    'msg_id'    => 124578,
    'room_id'   => 126,
    'author_id' => 12753,
    'text'      => 'Hello!',
    'date'      => date('Y-m-d H:m:i'),
);

$user_id = 'user_12754';
$dm = 'messages';

$con = new MongoClient();
$collection= $con->$dm->$user_id;
//$collection->insert($data);

//$collection= $con->messages->$user_id;
//$collection->remove();
// Выбрать все
$filter=array("room_id"=> 126);
$list = $collection->find($filter)->sort(array('msg_id'=>-1))->limit(1);
while($document = $list->getNext())
{
    echo '<pre>'; print_r($document); echo '</pre>';
}
$con->close();

/*
 *
// Выбрать один
$person = $collection->findOne();
echo $person["name"];


// фильтрация по названию компании
$filter=array("company.name"=> "Microsoft");
$person = $collection->findOne($filter);


// Выбрать все
$list = $collection->find();
while ($document = $list->getNext()) {
    echo '<pre>'; print_r($document); echo '</pre>';
}
 *
 * */

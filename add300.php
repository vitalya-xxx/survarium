<?php
require("config.inc.php");



function addUser( $data ) {
$names = array("Edan","Abraham","Dillon","Anthony","Paki","John","Alden","Dominic","Steven","Castor","Bruce","Abraham","Zachary","Jared","Keegan","Colorado","Prescott","Amal","Michael","Aaron","Basil","Lane","Garrett","Marshall","Hyatt","Lucian","Hayes","Odysseus","Stone","Simon","Callum","Forrest","Raja","Mason","Lionel","Dennis","Brady","Beck","Jeremy","Jason","Harding","Kaseem","Daquan","Sylvester","Lev","Tucker","Bert","Owen","Clinton","Damian","Hayes","Curran","Phillip","Quinn","Boris","Brody","Hop","Armand","Bernard","Nasim","Rashad","Byron","Sebastian","Curran","Alden","Honorato","Benjamin","Gary","Ferdinand","Hamilton","Lee","Cooper","Garrett","Tyler","Richard","Rudyard","Kato","Cade","Walker","Elton","Peter","Walker","Vladimir","Linus","Forrest","Martin","Marvin","Brendan","Kasper","Nash","Wing","Fletcher","Levi","Alexander","Beau","Daquan","Kareem","Jason","Raymond","Garrett","Rahim","August","Forrest","Tanner","Micah","Kasper","Rashad","Murphy","Brett","Timon","Orlando","Buckminster","Vance","Noah","Kennedy","Kennan","Abel","Bruce","Elliott","Octavius","Brock","Octavius","Timon","Tanner","Paki","Ashton","Hyatt","Lucius","Christian","Alfonso","Ian","Macaulay","Scott","Kennan","Ryan","Harper","Nash","Adrian","Bruno","Kennedy","Carlos","Linus","Thaddeus","Calvin","Davis","Harrison","Aristotle","Holmes","Linus","Armando","Dennis","Kasimir","Nissim","Nehru","Caleb","Xavier","William","Dante","Jacob","Stone","Kermit","Wyatt","Raphael","Bradley","Lev","Bruce","Xenos","Ezekiel","Hakeem","Xenos","Prescott","Bevis","Adrian","Zephania","Felix","Lester","Reese","Hu","Gannon","Kermit","Lester","Eaton","Lucius","Kamal","Steven","Raja","Tanek","Xenos","Hashim","Leo","Jameson","Davis","Stewart","Colin","Arsenio","Marshall","Henry","Austin","Jonas","Keaton");
		for ($i = 1; $i <= $data; $i++) {
		$sql = mysql_query("INSERT INTO `users` (`user_nickname`, `user_level`,`user_icon`,`user_isOnline`) 
								VALUES ('".$names[rand(1,190)].rand(150000, 1500000)."','".rand(1, 72)."','src/img/avatar".rand(1, 5).".jpg','".rand(0, 1)."')");
			//Если вставка прошла успешно
			if ($sql) {
				echo "OK".$i;
			} else {
				echo "NOOOO!";
			}
		}
}

function addFriends( $user,$friends ) {

		for ($i = 1; $i <= $friends; $i++) {
			$rand = rand(1315, 12000);
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$user."','".$rand."')");
		$sql = mysql_query("INSERT INTO `friends` (`user_id`, `user_id_friend`) 
								VALUES ('".$rand."','".$user."')");
			//Если вставка прошла успешно
			if ($sql) {
				echo "F OK".$i;
			} else {
				echo "NOOOO!";
			}
		}
}

if($_GET["u"]){
	addUser($_GET["u"]);
	}
if($_GET["user"] && $_GET["f"]){
	addFriends($_GET["user"],$_GET["f"]);
	}


?> 


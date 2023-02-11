<?php
	$fileLogin = '../login.php';

	//session_start();
	if(!isset($_SESSION['userID'])){
		echo '
			<br /><br />
			<center><font face="Verdana" size="2" color="red">Sorry, please login and use this page </font></center>
			<br /><br />
			<center><a href="'.$fileLogin.'">Login</a></center>
			';
		exit;
	}
?>
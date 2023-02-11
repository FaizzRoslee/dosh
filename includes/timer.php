<?php
	$fileLogout = '../logout.php';

	$_SESSION['time_out'] = $_SESSION['timer']; // from login.php
	$newtime = mktime(); 
	$_SESSION['tdiff'] = $newtime - $_SESSION['lastaccess'];
	if ($_SESSION['tdiff'] > $_SESSION['time_out']){
		echo '
			<script type="text/javascript">
				alert("Sorry, please login and use this page!");
				window.location.href="'.$fileLogout.'";
			</script>
			';
		exit;
	}

	$_SESSION['lastaccess'] = $newtime;
?>
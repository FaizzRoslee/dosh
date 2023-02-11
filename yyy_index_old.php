<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
        "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
	<title><?= $sys_config['system_name'].' version: '.$sys_config['version'] ?></title>
	<!--<META http-equiv="Refresh" content="60">-->
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0"> 
	<meta http-equiv="cache-control" content="no-cache">
</head>
	<!--
	<frameset cols="100%">
			

		<frame src="authentication/login.php">
			
	</frameset>
	-->
<?php
header("Location: authentication/login.php");
 
exit;
?>
	
	
</html>



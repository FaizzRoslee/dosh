<?php
	require_once $sys_config['includes_path'].'check.php';
	require_once $sys_config['includes_path'].'timer.php';
?>

<html>
<head>
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="0"> 
	<meta http-equiv="cache-control" content="no-cache">
	<!--<link rel="stylesheet" type="text/css" href="example.css"/>-->
	<!--<link rel="stylesheet" type="text/css" href="<?= $includesPath ?>tooltips/tooltips_style.css" />-->
	<!--<script type="text/javascript" src="sortable.js"></script>-->
	<script language="JavaScript"><!--
		browser_version= parseInt(navigator.appVersion);
		browser_type = navigator.appName;
		
		if (browser_type == "Microsoft Internet Explorer" && (browser_version >= 4)) {
		document.write("<link rel='stylesheet' href='<?= $sys_config['menu_path'] ?>css/style-green-ie.css' type='text/css'>");
		document.write("<link rel='stylesheet' href='<?= $sys_config['includes_path'] ?>css/page_style-ie.css' type='text/css'>");
		}
		
		else if (browser_type == "Netscape" && (browser_version >= 4)) {
		document.write("<link rel='stylesheet' href='<?= $sys_config['menu_path'] ?>css/style-green.css' type='text/css'>");
		document.write("<link rel='stylesheet' href='<?= $sys_config['includes_path'] ?>css/page_style.css' type='text/css'>");
		}
	// --></script>
		
	<link rel="stylesheet" href="root_style.css" >
</head>
<body leftmargin="0" rightmargin="0" bottommargin="0">

	<table width="100%" border="0" align="center">
	<tr>
		<td align="center"><img src="<?= $sys_config['images_path'] ?>banner.jpg" /></td>
	</tr>
	</table>
	
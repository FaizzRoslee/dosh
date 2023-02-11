<?php
	//****************************************************************/
	// filename: help.php
	// description: tip for field
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================

	$help = $_GET['help'];
	
	if($help=='pilih'){
		echo _LBL_HELP_CHOOSE;
	}else{
		echo '';
	}
?>

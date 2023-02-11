<?php
	//****************************************************************/
	// filename: help.php
	// description: tip for field
	//****************************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================

	$help = $_GET["help"];
	
	if($help=="compNo"){
		echo _LBL_HELP_COMP_NO .":<br />";
		echo "1) 1234567-A<br />";
		echo "2) A1234567-B<br />";
	}elseif($help=="email"){
		echo "1) abc@yahoo.com<br />";
		echo "2) abc_def@gmail.com<br />";
	}elseif($help=="postcode"){
		echo _LBL_HELP_POSTCODE;
	}elseif($help=="phone" || $help=="fax"){
		echo "+603-12345678<br />";
	}elseif($help=="mobile"){
		echo "+6012-3456789<br />";
	}elseif($help=="pilih"){
		echo _LBL_HELP_CHOOSE;
	}else{
		echo "";
	}
?>

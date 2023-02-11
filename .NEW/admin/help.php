<?php
	//****************************************************************/
	// filename: help.php
	// description: tip for field
	//****************************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";

	$help = $_GET["help"];
	
	if($help=="sCode"){
		echo _LBL_HELP_STATE_CODE;
	}elseif($help=="pictogram"){
		echo _LBL_HELP_PICTOGRAM;
	}elseif($help=="showPic"){
		$pictogramID = $_GET["pID"];
	
		$dataColumns = "Pictogram_Url";
		$dataTable = "tbl_Hazard_Pictogram";
		$dataWhere = array("AND Pictogram_ID" => " = ".quote_smart($pictogramID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,"",$dataWhere); 
		$picUrl = "";
		foreach($arrayData as $detail){
			$picUrl = $detail[$dataColumns];
		}
		
		if($picUrl!="") $imgSrc = $picUrl;
		else $imgSrc = $sys_config["images_path"]."no_image.gif";
		
		echo '<img src="'.$imgSrc.'" width="150" height="150" />';
	}elseif($help=="pic" || $help=="hSttmnt"){
		echo _LBL_HELP_APPEAR_IF;
	}elseif($help=="showSttmnt"){
		$statementID = $_GET["sID"];
	
		$dataColumns = "Statement_Desc";
		$dataTable = "tbl_Hazard_Statement";
		$dataWhere = array("AND Statement_ID" => " = ".quote_smart($statementID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,"",$dataWhere); 
		$desc = "";
		foreach($arrayData as $detail){
			$desc = $detail[$dataColumns];
		}
		
		echo $desc;
	}elseif($help=="compNo"){
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
	}else{
		echo "";
	}

?>

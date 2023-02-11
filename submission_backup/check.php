<?php
	/***************************************************/
	// filename 	: 
	// description 	: 
	/***************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	//==================================
	if(isset($_POST["cID"])) {
		$cID = $_POST["cID"];
		$classID = _get_StrFromCondition("tbl_Hazard_Category","Class_ID","Category_ID",$cID);
		
		$dataColumns = "Category_ID";
		$dataTable = "tbl_Hazard_Category";
		$dataJoin = "";
		$dataWhere = array("AND Class_ID" => "= ".quote_smart($classID),"AND Category_ID"=>"!= ".quote_smart($cID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		
		$cIDs = "";
		foreach($arrayData as $detail){
			$cIDs .= ($cIDs!="")? ",".$detail["Category_ID"] : $detail["Category_ID"];
		}
		// echo $cIDs;
		$arr = array(
					"cIDs" => $cIDs
				);
		echo json_encode($arr);
	}
	
?>
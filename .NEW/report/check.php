<?php
	/***************************************************/
	// filename 	: 
	// description 	: 
	/***************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if(isset($_POST["type"]) && isset($_POST["state"]) && isset($_POST["comp"])) {
		$type = $_POST["type"];
		$state = $_POST["state"];
		$comp = $_POST["comp"];
		
		if($type=="all") $type = "6,7,8";
		
		if(!empty($type)){
			$sub 		= "SELECT Usr_ID FROM sys_User WHERE Level_ID IN (". quote_smart($type,false) .")";
			$dColumns 	= "Usr_ID,Company_Name";
			$dTable 	= "sys_User_Client";
			$dJoin 		= "";
			$dWhere 	= array(
							"AND Usr_ID" => "IN (". $sub .")",
							"AND (SELECT Active FROM sys_User WHERE Usr_ID = $dTable.Usr_ID)" => "= 1",
							"AND (SELECT isDeleted FROM sys_User WHERE Usr_ID = $dTable.Usr_ID)" => "= 0"
						);
			if($userLevelID==5){
				$dWhere = array_merge($dWhere,array("AND State_Reg"=>"IN ($state)"));
			}
			else{
				$dWhere = array_merge($dWhere,array("AND State_Reg"=>"IN (". $state .")"));
			}
			$dWhere 	= array_merge($dWhere,array("GROUP BY"=>"Usr_ID"));
			
			// print_r($dWhere);
			
			$dOrder 	= "Company_Name ASC";
			$arrayData 	= _get_arrayData($dColumns,$dTable,$dJoin,$dWhere,$dOrder);
		}
		else $arrayData = array();
		
		// echo '<option value="" '. (($comp=="")?'selected':'') .' >'._LBL_SELECT_ALL.'</option>';
		echo '<option value="0" selected="selected" >'._LBL_SELECT_ALL.'</option>';
		foreach($arrayData as $detail){
			echo '<option value="'.$detail['Usr_ID'].'" '. (($comp==$detail['Usr_ID'])?'selected':'') .' >'. strtoupper($detail['Company_Name']) .'</option>';
		}
		/*
		echo $cID;
		*/
	}
	
?>
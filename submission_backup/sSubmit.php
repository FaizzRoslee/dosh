<?php
	//****************************************************************/
	// filename: sSubmit.php
	// description: view the submission detail
	//****************************************************************/
	//include_once '../includes/sys_config.php';
	/*****/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*****/
	$remark = isset($_POST["remark"])?$_POST["remark"]:"";
	if($statusID==1){
		/*****/
		$currentYear = date("Y");
		$sqlIns = "INSERT INTO tbl_Submission_Submit (Submission_ID,SubmitBy,SubmitTime)"
				. " VALUES (".quote_smart($submissionID).",".quote_smart($Usr_ID).",NOW())";
		$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
		$submitID = $db->sql_nextid();
		/*****/ 
		// count submission for current year
		$submitID = _generateSubmissionID($submitID);
		/*****/
	}
	
	/****************************
	 * add /RS for resubmit submission; update on 20141031
	 ****************************/
	if($statusID==2){
		$subSubmitID = isset($_POST["subSubmitID"])?$_POST["subSubmitID"]:"";
		if(!empty($subSubmitID)){
			$ar_subNo	= explode("/",$subSubmitID);
			if(is_numeric($ar_subNo[ count($ar_subNo)-1 ]))
				$indexLast = count($ar_subNo);
			else $indexLast = count($ar_subNo)-1;
				
			$ar_subNo[ $indexLast ] = "RS";
			$subSubmitID = implode("/",$ar_subNo);
		}
		$submitID = $subSubmitID;
	}
	/****************************/
	
	/*****/
	$sqlUpd = "UPDATE tbl_Submission SET"
			. " Status_ID = ". quote_smart($newStatusID) .",";
	if($statusID==1 || $statusID==2){
		$sqlUpd .= " Submission_Submit_ID = ". quote_smart($submitID) .",";
	}
	$sqlUpd .= " UpdateBy = ". quote_smart($Usr_ID) .","
			. " UpdateDate = NOW()"
			. " WHERE Submission_ID = ". quote_smart($submissionID) ." LIMIT 1";
	$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
	/*****/
	if($resUpd){
	
		$msg = "Submission was sent for checking."; 
		/*****/
		$sqlIns = "INSERT INTO tbl_Submission_Record (Sub_Record_Desc,Sub_Record_Remark,Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID)"
				. " VALUE (".quote_smart($msg).",".quote_smart($remark).",".quote_smart($Usr_ID).",NOW(),".quote_smart($newStatusID).",".quote_smart($submissionID).")";
		$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
		/*****/
		func_add_audittrail($Usr_ID,$msg." [".$submissionID."]","Submission","tbl_Submission,".$submissionID);
		if($statusID==1) $msg .= " Submission ID: [".$submitID."]"; 
		/*****/
		$cat = (($newStatusID==11)? "5" : "6" ); //5 is for new/6 is for renew
		$content = _get_Notification($cat,$Usr_ID,$submissionID); //
		if(is_array($content)){
			if(!empty($content["email"])){
				func_email2($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
			}
			// else{
				// echo '
					// <script language="Javascript">
					// alert("'. _LBL_NO_EMAIL .'");
					// </script>
					// ';
			// }
		}

	}
	else{
		$msg = "Submission cannot sent for checking"; 
		func_add_audittrail($Usr_ID,$msg ." [". $submissionID ."]","Submission","tbl_Submission,".$submissionID);
	}
	// die;
	/*****/
	echo "<script language=\"Javascript\">"
		."alert('$msg');"
		."parent.left.location.href = '". $sys_config["frame_path"] ."left_submission.php?menu=$menu';"
		."location.href = '$file';"
		."</script>"
		;
	exit();
	/*****/
?>
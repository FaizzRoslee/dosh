<?php
	//****************************************************************/
	// filename: sHistory.php
	// description: view the history of submission
	//****************************************************************/
	//include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	include_once $sys_config["includes_path"]."func_date.php";
	/*********/
	$arrayData = "";
	/*********/
	if($submissionID!=0){
		$dataColumns = "Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,Sub_Record_Contact,Sub_Record_Status,Sub_Record_Date,Sub_Record_By";
		$dataTable = "tbl_Submission_Record";
		$dataJoin = "";
		$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID));
		$dataOrder = "Sub_Record_ID";
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder); 
	}
	/*********/
	$textAlignCenter = "text-align:center";
?>	
	
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="5%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_NUMBER ?></td>
			<td width="25%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_DESCRIPTION ?></td>
			<td width="30%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_REMARK ?></td>
			<td width="20%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_STATUS ?></td>
			<td width="20%" class="label" style="<?= $textAlignCenter ?>"><?= (($new==5||$new==15) ? _LBL_DATE : _LBL_RECORD_BY_DATE) ?></td>
		</tr>
		<?php
			//print_r($arrayData);
			$no = 0;
			if(is_array($arrayData)){
				foreach($arrayData as $index => $detail){
					$no = $index+1;
		?>
		<tr class="contents" height="30">
			<td align="center"><label><?= $no; ?></label></td>
			<td><label><?= ($detail["Sub_Record_Desc"]!="")?$detail["Sub_Record_Desc"]:"-"; ?></label></td>
			<td><label><?= ($detail["Sub_Record_Remark"]!="")?nl2br($detail["Sub_Record_Remark"]):"-"; ?></label><br /><br /><?php
				echo ($detail["Sub_Record_Reason"]!="")?"[<strong><i>". _LBL_REASON .":</i></strong> ". nl2br($detail["Sub_Record_Reason"]) ."]":""; 
				echo "<br />";
				echo ($detail["Sub_Record_Contact"]!="")?"[<strong><i>". _LBL_CONTACT_NO .":</i></strong> ". $detail["Sub_Record_Contact"] ."]":""; 
			?></td>
			<td align="center"><label><?php
				echo _get_StrFromCondition("tbl_Submission_Status","Status_Desc","Status_ID",$detail["Sub_Record_Status"]); 
				echo "</label><br />";
				// echo "[". _get_StrFromCondition("tbl_Submission_Status","Status_Type","Status_ID",$detail["Sub_Record_Status"]) ."]"; 
			?></td>
			<td align="center"><label><?php
				if($new==5||$new==15){ // for state hide name 
					echo (($detail["Sub_Record_Date"]!="")?func_ymd2dmytime($detail["Sub_Record_Date"]):"-"); 
				}else{
					echo _get_User($detail["Sub_Record_By"])."<br />[".(($detail["Sub_Record_Date"]!="")?func_ymd2dmytime($detail["Sub_Record_Date"]):"-")."]"; 
				}
			?></label></td>
		</tr>
		<?php
				}
			}
			if($no==0){
		?>
		<tr class="contents">
			<td align="center" colspan="5"><br /><label><?= _LBL_NO_RECORD; ?></label><br />&nbsp;</td>
		</tr>
		<?php
			}
		?>
	</table>
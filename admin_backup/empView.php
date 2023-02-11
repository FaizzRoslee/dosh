<?php
	//****************************************************************/
	// filename: empAddEdit.php
	// description: add/edit employee details
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*********/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*********/
	func_header("",
				"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*********/
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID>2) exit();
	/*********/
	if(isset($_POST["Usr_ID"])) $Usr_ID = $_POST["Usr_ID"];
	elseif(isset($_GET["Usr_ID"])) $Usr_ID = $_GET["Usr_ID"];
	else $Usr_ID = 0;
	/*********/
	$staffID = "";
	$staffName = "";
	$staffDesignation = "";
	$staffHead = "";
	$staffLevel = "";
	$staffState = "";
	$staffEmail = "";
	$staffPhone = "";
	$picName = $picEmail = $picNo = "";
	$active = 1;
	/*********/
	if($Usr_ID!=0){
		$dataColumns = "User_StaffID,User_Fullname,User_Designation,User_Head,Level_ID,State_ID,User_Email,User_PhoneNo,PIC_Name,PIC_Email,PIC_ContactNo,Active";
		$dataTable = "sys_User u";
		$dataJoin = array("sys_User_Staff us"=>"u.Usr_ID = us.Usr_ID");
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$staffID 	= $detail["User_StaffID"];
			$staffName 	= $detail["User_Fullname"];
			$staffDesignation = $detail["User_Designation"];
			$staffHead 	= $detail["User_Head"];
			$staffLevel = $detail["Level_ID"];
			$staffState = $detail["State_ID"];
			$staffEmail = $detail["User_Email"];
			$staffPhone = $detail["User_PhoneNo"];
			$picName = $detail["PIC_Name"];
			$picEmail = $detail["PIC_Email"];
			$picNo = $detail["PIC_ContactNo"];
			$active 	= $detail["Active"];
		}
	}
	/*********/
?>
	<script language="Javascript">
	$(document).ready(function() { 
		$("label[title]").each(function() {
			$(this).qtip({
				//content: { title: { text: $(this).attr("title") } },
				position: { corner: { tooltip: "bottomLeft", target: "topLeft" } },
				style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2( _LBL_STAFF .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('Usr_ID',$Usr_ID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STAFF_ID ?></td>
			<td width="80%"><label><?= $staffID; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_NAME ?></td>
			<td><label><?= $staffName; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESIGNATION ?></td>
			<td><label><?= $staffDesignation; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_LEVEL ?></td>
			<td><label><?= _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$staffLevel);?></label></td>
		</tr>
		<?php if($staffLevel==3){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_HEAD ?></td>
			<td><label><?= (!empty($staffHead) || $staffHead!=0) ? _get_StrFromCondition('sys_User_Staff','User_Fullname','Usr_ID',$staffHead) : '-' ;?></label></td>
		</tr>
		<?php } ?>
		<?php if($staffLevel==5){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><label><?= _get_StrFromCondition('sys_State','State_Name','State_ID',$staffState);?></label></td>
		</tr>
		<?php } ?>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td><label><?= $staffEmail; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CONTACT_NO ?></td>
			<td><label><?= $staffPhone; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><label><?php 
				foreach($arrStatusAktif as $detail){
					if($detail['value']==$active) echo $detail['label']; 
				}
			?></label></td>
		</tr>
	</table>
	<?php if($staffLevel==5){ ?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label" colspan="2"><?= _LBL_PERSON_IN_CHARGE ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME ?></td>
			<td width="80%"><label><?= $picName; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td><label><?= $picEmail; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CONTACT_NO ?></td>
			<td><label><?= $picNo; ?></label></td>
		</tr>
	</table>
	<?php } ?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('empAddEdit.php?Usr_ID=$Usr_ID');");
				echo form_button("edit",_LBL_EDIT,$dOthers);
				/*********/
				echo "&nbsp;";
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('empList.php');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
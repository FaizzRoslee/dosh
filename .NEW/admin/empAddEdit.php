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
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	if($Usr_ID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	if(isset($_POST['save'])){
		$staffID 			= isset($_POST["staffID"])			? $_POST["staffID"]			: "";
		$staffName 			= isset($_POST["staffName"])		? $_POST["staffName"]		: "";
		$staffDesignation 	= isset($_POST["staffDesignation"])	? $_POST["staffDesignation"]: 0;
		$staffLevel 		= isset($_POST["staffLevel"])		? $_POST["staffLevel"]		: 0;
		$staffHead 			= isset($_POST["staffHead"])		? $_POST["staffHead"]		: "";
		$staffState			= isset($_POST["staffState"])		? $_POST["staffState"]		: "";
		
		$picName		= isset($_POST["picName"])		? $_POST["picName"]		: "";
		$picEmail		= isset($_POST["picEmail"])		? $_POST["picEmail"]		: "";
		$picNo			= isset($_POST["picNo"])		? $_POST["picNo"]		: "";
		
		if($staffState=="") $staffState = 0;
		$active 		= isset($_POST["active"])		?$_POST["active"]		: 0;
		if($staffHead=="") $staffHead = 0;
		$staffEmail		= isset($_POST["staffEmail"])	? $_POST["staffEmail"]	: "";
		$staffPhone		= isset($_POST["staffPhone"])	? $_POST["staffPhone"]	: "";
		/*********/
		$updBy 		= $_SESSION["user"]["Usr_ID"];
		/*********/
		$checkLoginID	= _get_RowExist(
								"sys_User",
								array("AND LOWER(Usr_LoginID)"=>"= ".quote_smart(strtolower($staffID)),"AND isDeleted"=>"= 0","AND Usr_ID"=>"!= ".quote_smart($Usr_ID)),
								''
						);//$table='', $where=array(), $others=array()
		if($checkLoginID>0){
			$msg = '';
			if($checkLoginID>0) $msg .= "Login ID is not available\n";
			/*********/
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				</script>
				';
		}
		else{
			if($Usr_ID!=0){
				$sqlUpd	= "UPDATE sys_User SET"
						. " Usr_LoginID = ".quote_smart(strtoupper($staffID)).","
						. " Level_ID = ".quote_smart($staffLevel).","
						. " UpdateBy = ".quote_smart($updBy)."," 
						. " UpdateDate = NOW()," 
						. " Active = ".quote_smart($active)."" 
						. " WHERE Usr_ID = ".quote_smart($Usr_ID)." LIMIT 1";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//echo $sqlUpd;
				/*********/
				$sqlUpd = "UPDATE sys_User_Staff SET"
						. " User_Fullname = ".quote_smart($staffName).","
						. " User_Designation = ".quote_smart($staffDesignation).","
						. " User_StaffID = ".quote_smart(strtoupper($staffID)).","
						. " User_Head = ".quote_smart($staffHead)."";
				if($staffLevel==5) $sqlUpd .= " ,State_ID = ". quote_smart($staffState) ."";
				else $sqlUpd .= " ,State_ID = NULL";
				$sqlUpd .= " ,User_Email = ". quote_smart($staffEmail) ."";
				$sqlUpd .= " ,User_PhoneNo = ". quote_smart($staffPhone) ."";
				
				$sqlUpd .= " ,PIC_Name = ". quote_smart($picName) ."";
				$sqlUpd .= " ,PIC_Email = ". quote_smart($picEmail) ."";
				$sqlUpd .= " ,PIC_ContactNo = ". quote_smart($picNo) ."";
				
				$sqlUpd .= " WHERE Usr_ID = ".quote_smart($Usr_ID)." LIMIT 1";
				//echo $sqlUpd;
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*********/
				$msg = ($resUpd) ? "Update Successfully" : "Update Unsuccessfully";
				/*********/
			}
			else{
				$sqlIns	= "INSERT INTO sys_User ("
						. " Usr_LoginID, Usr_Password, Level_ID,"
						. " RecordBy, RecordDate, UpdateBy, UpdateDate, Active" 
						. " ) VALUES ("
						// . " ".quote_smart(strtoupper($staffID)).",".quote_smart(md5('password')).",".quote_smart($staffLevel).","
						. " ".quote_smart(strtoupper($staffID)).",".quote_smart(md5(strtoupper($staffID))).",".quote_smart($staffLevel).","
						. " ".quote_smart($updBy).", NOW(), ".quote_smart($updBy).", NOW(), ".quote_smart($active).""
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$Usr_ID = $db->sql_nextid();
				/*********/
				$sqlIns = "INSERT INTO sys_User_Staff ("
						. " User_Fullname,User_Designation,"
						. " User_StaffID,User_Head,Usr_ID";
				$sqlIns .= " ,State_ID";
				$sqlIns .= " ,User_Email";
				$sqlIns .= " ,User_PhoneNo";
				
				$sqlIns .= " ,PIC_Name";
				$sqlIns .= " ,PIC_Email";
				$sqlIns .= " ,PIC_ContactNo";
				
				$sqlIns .= " ) VALUES ("
						. " ".quote_smart($staffName).",".quote_smart($staffDesignation).","
						. " ".quote_smart(strtoupper($staffID)).",".quote_smart($staffHead).",".quote_smart($Usr_ID)."";
				if($staffLevel==5) 
					$sqlIns .= " ,". quote_smart($staffState) ."";
				else
					$sqlIns .= " ,NULL";
				$sqlIns .= " ,". quote_smart($staffEmail) ."";
				$sqlIns .= " ,". quote_smart($staffPhone) ."";
				
				$sqlIns .= " ,". quote_smart($picName) ."";
				$sqlIns .= " ,". quote_smart($picEmail) ."";
				$sqlIns .= " ,". quote_smart($picNo) ."";
				
				$sqlIns .= " )";
				//echo $sqlIns;//die;
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*********/
				$msg = ($resIns) ? "Insert Successfully\\nDefault password is user Staff ID" : "Insert Unsuccessfully";
				/*********/
			}
			/*********/
			func_add_audittrail($updBy,$msg." [".$Usr_ID."]","Admin-User","sys_User,".$Usr_ID);//($recordBy="",$recordDesc="",$recordMod="",$recordOther="")
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				location.href="empList.php";
				</script>
				';
			exit();
			/*********/
		}
	}
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
			$active = $detail["Active"];
		}
	}
	/*********/
?>
	<script language="Javascript">
	$(document).ready(function() {
		
		$(".cls-officer, .cls-enforcement").hide();
		$("#staffLevel").on("change",function(){
			if( $(this).val()== 5 ){
				$(".cls-officer").hide();
				$(".cls-enforcement").show();
			}
			else if( $(this).val()== 3 ){
				$(".cls-officer").show();
				$(".cls-enforcement").hide();
			}
			else $(".cls-officer, .cls-enforcement").hide();
			
			if( !$(".cls-enforcement").is(":visible") ){
				// alert("enforcement visible");
				$(".cls-enforcement").find("input, select").val("");
			}
			if( !$(".cls-officer").is(":visible") ){
				// alert("officer visible");
				$(".cls-officer").find("input, select").val("");
			}
		}).trigger("change");
	});
	
	function showHide(){
		var d = document;
		var staffLevel = d.getElementById('staffLevel').value;
		if(staffLevel==5)
			d.getElementById('tr_state').style.display = '';
		else
			d.getElementById('tr_state').style.display = 'none';
	}
	</script>
	<?php func_window_open2( _LBL_STAFF .' :: '.$addEdit); ?>
	<form name="myForm" action="" method="post">
	<div id="div_title">
	<?= form_input('Usr_ID',$Usr_ID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STAFF_ID ?></td>
			<td width="80%"><?php
				$param1 = 'staffID';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$staffID;
				$param3 = array('type'=>'text','title'=>_LBL_TITLE_STAFFID);
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_NAME ?></td>
			<td><?php
				$param1 = 'staffName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$staffName;
				$param3 = array('type'=>'text','style'=>'width:200px');
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESIGNATION ?></td>
			<td><?php
				$param1 = 'staffDesignation';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$staffDesignation;
				$param3 = array('type'=>'text','style'=>'width:200px');
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_LEVEL ?></td>
			<td><?php
				$param1 = "staffLevel";
				$param2 = "";
				$param3 = _get_arraySelect("sys_Level","Level_ID","Level_Name",array("AND Level_Type"=>"= 1"));//($tblName="",$colID="",$colDesc="",$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$staffLevel;
				$param5 = array("onChange"=>"showHide();");
				echo form_select($param1,$param2,$param3,$param4,$param5);
				$sLevel = $param4;
			?></td>
		</tr>
		<tr class="contents cls-officer">
			<td class="label"><?= _LBL_HEAD ?></td>
			<td><?php
				$param1 = "staffHead";
				$param2 = "";
				$param3 = array();
				$sql = "SELECT u.Usr_ID,User_Fullname FROM sys_User u"
					. " LEFT OUTER JOIN sys_User_Staff us ON u.Usr_ID = us.Usr_ID"
					. " WHERE 1 AND u.Level_ID = 4 AND Active = 1 AND isDeleted = 0";
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				while($row=$db->sql_fetchrow($res)){
					$param3[$row["Usr_ID"]]["label"] = $row["User_Fullname"];
					$param3[$row["Usr_ID"]]["value"] = $row["Usr_ID"];
				}
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$staffHead;
				$param5 = array("title"=>_LBL_TITLE_HEAD);
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr id="tr_state" class="contents" style="display:<?= ($sLevel==5)?'':'none' ?>">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><?php
				$param1 = 'staffState';
				$param2 = '';
				$param3 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND Active'=>'= 1','AND isDeleted'=>'= 0'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$staffState;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td><?php
				$param1 = 'staffEmail';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$staffEmail;
				$param3 = array('type'=>'text','style'=>'width:200px','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."')");
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CONTACT_NO ?></td>
			<td><?php
				$param1 = 'staffPhone';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$staffPhone;
				$param3 = array('type'=>'text','style'=>'width:200px');
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
				$param1 = 'active';
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$active;
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
	</table>
	<div class="cls-enforcement">
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label" colspan="2"><?= _LBL_PERSON_IN_CHARGE ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME ?></td>
			<td width="80%"><?php
				$param1 = 'picName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$picName;
				$param3 = array('type'=>'text','style'=>'width:200px');
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td><?php
				$param1 = 'picEmail';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$picEmail;
				$param3 = array('type'=>'text','style'=>'width:200px','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."')");
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CONTACT_NO ?></td>
			<td><?php
				$param1 = 'picNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$picNo;
				$param3 = array('type'=>'text','style'=>'width:200px');
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
	</table>
	</div>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"submit");
				echo form_button("save",_LBL_SAVE,$dOthers);
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
	</div>
	</form>
	<?php func_window_close2(); ?>
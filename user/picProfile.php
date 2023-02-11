<?php
	//****************************************************************/
	// filename: userAddEdit.php
	// description: detailed of the user
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
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				"", // css	
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	//==========================================================
	if(isset($_POST["savedata"])){
		// print_r($_POST);
		$picName 	= isset($_POST["picName"])	 ? $_POST["picName"] 	: "";
		$picEmail 	= isset($_POST["picEmail"]) ? $_POST["picEmail"] 	: "";
		$picNo		 = isset($_POST["picNo"]) 	? $_POST["picNo"] 		: "";
		
		$updBy = $Usr_ID;
		
		$sqlUpd = "UPDATE sys_User_Staff SET"
				. " PIC_Name = ". quote_smart($picName)
				. " ,PIC_Email = ". quote_smart($picEmail)
				. " ,PIC_ContactNo = ". quote_smart($picNo)
				. " WHERE Usr_ID = ". quote_smart($Usr_ID);
		$resUpd = $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
		//===============================================================================
		if($resUpd){ $msg = 'Update Successfully'; }
		else{ $msg = 'Update Unsuccessfully'; }
		
		func_add_audittrail($updBy,$msg.' ['.$Usr_ID.']','Person In Charge-Profile','sys_User,'.$Usr_ID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href= "'. $_SERVER["PHP_SELF"] .'";
			</script>
			';
		exit();
	}
	
	$picName = $picEmail = $picNo = "";
	if($Usr_ID!=0){
		$dataColumns = 'PIC_Name,PIC_Email,PIC_ContactNo';
		$dataTable = 'sys_User u';
		$dataJoin = array('sys_User_Staff us'=>'u.Usr_ID = us.Usr_ID');
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//==========================================================
		foreach($arrayData as $detail){
			$picName	= $detail['PIC_Name'];
			$picEmail	= $detail['PIC_Email'];
			$picNo 		= $detail['PIC_ContactNo'];
		}
	}
	
	$legendStyle = 'font-weight:bold;font-size:12px;';
	$requiredField = '<span style="color:red"> * </span>';
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
		
		$(".cls-input, .cls-btn-save").hide();
		$("#edit").on("click",function(){
			$(".cls-btn-edit").hide();
			$(".cls-btn-save").show();
			
			$(".cls-label").hide();
			$(".cls-input").show();
		});
		$("#save").on("click",function(){
			$(".cls-btn-edit").show();
			$(".cls-btn-save").hide();
			
			$(".cls-label").show();
			$(".cls-input").hide();
			
			$("#myForm").append("<input type=\"hidden\" name=\"savedata\" />");
			$("#myForm").submit();
		});
		$("#cancel").on("click",function(){
			location.href = '<?= $_SERVER["PHP_SELF"] ?>';
		});
	});
	</script>
	<?php func_window_open2(_LBL_PERSON_IN_CHARGE); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td colspan="2" class="label"><?= _LBL_PERSON_IN_CHARGE ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME ?></td>
			<td width="80%"><span class="cls-label"><label><?= (!empty($picName)?$picName:"-") ?></label></span><span class="cls-input"><?= form_input("picName",$picName,array("style"=>"width:250px;")); ?></span></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_EMAIL ?></td>
			<td width="80%"><span class="cls-label"><label><?= (!empty($picEmail)?$picEmail:"-") ?></label></span><span class="cls-input"><?= form_input("picEmail",$picEmail,array("style"=>"width:250px;")); ?></span></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CONTACT_NO ?></td>
			<td width="80%"><span class="cls-label"><label><?= (!empty($picNo)?$picNo:"-") ?></label></span><span class="cls-input"><?= form_input("picNo",$picNo,array("style"=>"width:250px;")); ?></span></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><span class="cls-btn-edit"><?php
				$param1 = 'edit';
				$param2 = _LBL_EDIT;
				$param3 = array('type'=>'button');
				echo form_button($param1,$param2,$param3);
			?></span><span class="cls-btn-save"><?php
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'button');
				echo form_button($param1,$param2,$param3);
				
				echo "&nbsp;";
				
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button');
				echo form_button($param1,$param2,$param3);
			?></span></td>
		</tr>
	</table>
	<br />
	</form>
<?php	
	func_window_close2();
?>

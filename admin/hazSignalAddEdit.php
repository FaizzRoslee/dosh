<?php
	//****************************************************************/
	// filename: hazSignalAddEdit.php
	// description: add & edit the hazard signal
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
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				"",
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
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
	if(isset($_POST['signalID'])) $signalID = $_POST['signalID'];
	elseif(isset($_GET['signalID'])) $signalID = $_GET['signalID'];
	else $signalID = 0;
	//==========================================================
	if($signalID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$signal = isset($_POST['signal'])?$_POST['signal']:'';
		$active = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		if($signalID!=0){
			$sqlUpd	= "UPDATE tbl_Hazard_Signal SET"
					. " Signal_Name = ".quote_smart($signal).","
					. " UpdateBy = ".quote_smart($updBy)."," 
					. " UpdateDate = NOW()," 
					. " Active = ".quote_smart($active).""
					. " WHERE Signal_ID = ".quote_smart($signalID)." LIMIT 1";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			//===========================================================
			$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
		}else{
			$sqlIns	= "INSERT INTO tbl_Hazard_Signal ("
					. " Signal_Name,"
					. " UpdateBy, UpdateDate,"
					. " RecordBy, RecordDate,"
					. " Active" 
					. " ) VALUES ("
					. " ".quote_smart($signal).","
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($active).""
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$signalID = $db->sql_nextid();
			//===========================================================
			$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully';
		}
		//===========================================================
		func_add_audittrail($updBy,$msg.' ['.$signalID.']','Admin-Hazard Signal','tbl_Hazard_Signal,'.$signalID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="hazSignalList.php";
			</script>
			';
		exit();
		//===========================================================
	}
	//===========================================================
	$signal = '';
	$active = 1;
	//===========================================================
	if($signalID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Hazard_Signal';
		$dataWhere = array("AND Signal_ID" => " = ".quote_smart($signalID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere); 
		//===========================================================
		foreach($arrayData as $detail){
			$signal = $detail['Signal_Name'];
			$active = $detail['Active'];
		}
	}
	//===========================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_SIGNAL_WORD .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('signalID',$signalID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SIGNAL ?></td>
			<td width="80%"><?php
				$param1 = 'signal';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$signal;
				$param3 = array('type'=>'text');
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
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'hazSignalList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
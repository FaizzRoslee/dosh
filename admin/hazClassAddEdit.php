<?php
	//****************************************************************/
	// filename: hazClassAddEdit.php
	// description: add & edit the hazard classification [physical,health,environmental]
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
				$sys_config['includes_path']."javascript/js_validate.js,". // javascrip
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
	if(isset($_POST['classID'])) $classID = $_POST['classID'];
	elseif(isset($_GET['classID'])) $classID = $_GET['classID'];
	else $classID = 0;
	//==========================================================
	if($classID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$className = isset($_POST['className'])?$_POST['className']:'';
		$typeID = isset($_POST['typeID'])?$_POST['typeID']:0;
		$active = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		if($classID!=0){
			$sqlUpd	= "UPDATE tbl_Hazard_Classification SET"
					. " Class_Name = ".quote_smart($className).","
					. " Type_ID = ".quote_smart($typeID).","
					. " UpdateBy = ".quote_smart($updBy)."," 
					. " UpdateDate = NOW()," 
					. " Active = ".quote_smart($active).""
					. " WHERE Class_ID = ".quote_smart($classID)." LIMIT 1";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			//==========================================================
			$msg = ($resUpd) ? 'Insert Successfully' : 'Insert Unsuccessfully';
			//==========================================================
		}else{
			$sqlIns	= "INSERT INTO tbl_Hazard_Classification ("
					. " Class_Name, Type_ID,"
					. " UpdateBy, UpdateDate,"
					. " RecordBy, RecordDate,"
					. " Active" 
					. " ) VALUES ("
					. " ".quote_smart($className).",".quote_smart($typeID).","
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($active).""
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$classID = $db->sql_nextid();
			//==========================================================
			$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully';
			//==========================================================
		}
		//==========================================================
		func_add_audittrail($updBy,$msg.' ['.$classID.']','Admin-Hazard Classification','tbl_Hazard_Classification,'.$classID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="hazClassList.php";
			</script>
			';
		exit();
		//==========================================================
	}
	//==========================================================
	$className = '';
	$typeID = 0;
	$active = 1;
	//==========================================================
	if($classID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Hazard_Classification';
		$dataWhere = array("AND Class_ID" => " = ".quote_smart($classID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere); 		
		//==========================================================
		foreach($arrayData as $detail){
			$className = $detail['Class_Name'];
			$typeID = $detail['Type_ID'];
			$active = $detail['Active'];
		}
	}
	//==========================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_HAZARD_CLASSIFICATION .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('classID',$classID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CLASSIFICATION ?></td>
			<td width="80%"><?php
				$param1 = 'className';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$className;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_TYPE_NAME ?></td>
			<td><?php
				$param1 = 'typeID';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Type','Type_ID','Type_Name');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$typeID;
				echo form_select($param1,$param2,$param3,$param4,'');
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'hazClassList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
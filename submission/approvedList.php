<?php
	//****************************************************************/
	// filename: approvedList.php
	// description: list approved submission for renewing
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
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID==5) exit();
	//==========================================================
	func_header("",
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
	//=====================================================================
	if(isset($_POST['send'])){
	}
	//=====================================================================
?>
	<?php func_window_open2( _LBL_RENEW .' :: '. _LBL_ADD); ?>
	<form name="myForm" action="" method="post">
	<?php
		$dataColumns = '*';
		$dataTable = 'tbl_Submission';
		$dataJoin = '';
		$dataWhere["AND Status_ID"] = "IN (31,32,41,42,51,52)";
		if($userLevelID>=6) 
			$dataWhere["AND Usr_ID"] = "= ".quote_smart($Usr_ID);
		
		//extractArray($dataWhere);
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label" width="5%" style="text-align:center"><label><?= _LBL_NO; ?></label></td>
			<td class="label" width="50%"><label><?= _LBL_SUBMISSION_ID; ?></label></td>
			<td class="label" width="15%"><label><?= _LBL_CHEMICAL_TYPE; ?></label></td>
			<td class="label" width="15%" style="text-align:center"><label><?= _LBL_EXPIRED_DATE; ?></label></td>
			<td class="label" width="15%" style="text-align:center"><label><?= _LBL_CHOOSE ?></label></td>
		</tr>
		<?php
			$i=0;
			$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
			foreach($arrayData as $detail){
				if(isExpiredWithin($detail['Submission_ID'])==TRUE){
					$i++;
		?>
		<tr class="contents">
			<td align="center"><label><?= $i; ?></label></td>
			<td><label><?= $detail['Submission_Submit_ID']; ?></label></td>
			
			<td><label><?= _get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$detail['Type_ID']); ?></label></td>
			<td align="center"><label><?= _getExpiredDate($detail['Submission_ID']); ?></label></td>
			<td align="center"><label><a href="submissionrenewAddEdit.php?submissionID=<?= $detail['Submission_ID'] ?>&new=11"><?= _LBL_CHOOSE ?></a></label></td>
		</tr>
		<?php
				}
			}
			if($i==0){
		?>
		<tr class="contents">
			<td align="center" colspan="5"><br /><label><?= _LBL_NO_RECORD ?></label><br />&nbsp;</td>
		</tr>
		<?php
			}
		?>
	</table>
	<br />		
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'back';
				$param2 = _LBL_BACK;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'renewList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
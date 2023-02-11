<?php
	//****************************************************************/
	// filename: stateAddEdit.php
	// description: add/edit state information
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
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
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
	if(isset($_POST['stateID'])) $stateID = $_POST['stateID'];
	elseif(isset($_GET['stateID'])) $stateID = $_GET['stateID'];
	else $stateID = 0;
	//==========================================================
	if($stateID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$stateCode = isset($_POST['stateCode'])?$_POST['stateCode']:0;
		$stateName = isset($_POST['stateName'])?$_POST['stateName']:'';
		$active = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		$param1 = 'sys_State';
		$param2 = array('AND State_Code'=>"= ".quote_smart($stateCode),'AND State_ID'=>" != ".quote_smart($stateID),
						'AND Active'=>'= 1','AND isDeleted'=>'= 0');
		$param3 = '';
		$rowExist = _get_RowExist($param1,$param2,$param3);
		//==========================================================
		if($rowExist!=0){
			echo '
				<script language="Javascript">
				alert("'. _LBL_CODE_EXIST .'");
				//history.back();
				</script>
				';
		}else{
			if($stateID!=0){
				$sqlUpd	= "UPDATE sys_State SET"
						. " State_Code = ".quote_smart($stateCode).","
						. " State_Name = ".quote_smart($stateName).","
						. " UpdateBy = ".quote_smart($updBy)."," 
						. " UpdateDate = NOW()," 
						. " Active = ".quote_smart($active).""
						. " WHERE State_ID = ".quote_smart($stateID)." LIMIT 1";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
				//==========================================================
			}else{
				$sqlIns	= "INSERT INTO sys_State ("
						. " State_Code, State_Name,"
						. " UpdateBy, UpdateDate,"
						. " Active" 
						. " ) VALUES ("
						. " ".quote_smart($stateCode).",".quote_smart($stateName).","
						. " ".quote_smart($updBy).", NOW(),"
						. " ".quote_smart($active).""
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$stateID = $db->sql_nextid();
				//==========================================================
				$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully'; 
				//==========================================================
			}
			//==========================================================
			func_add_audittrail($updBy,$msg.' ['.$stateID.']','Admin-State','sys_State,'.$stateID);//($recordBy='',$recordDesc='',$recordMod='',$recordOther='')
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				location.href="stateList.php";
				</script>
				';
			exit();
			//==========================================================
		}
	}
	//==========================================================
	$stateCode = '';
	$stateName = '';
	$active = 1;
	//==========================================================
	if($stateID!=0){
		$dataColumns = '*';
		$dataTable = 'sys_State';
		$dataWhere = array("AND State_ID" => " = ".quote_smart($stateID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$stateCode = $detail['State_Code'];
			$stateName = $detail['State_Name'];
			$active = $detail['Active'];
		}
	}
	//==========================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		$('a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name: 'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_STATE .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('stateID',$stateID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STATE_CODE ?></td>
			<td width="80%"><?php
				$param1 = 'stateCode';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$stateCode;
				$param3 = array('type'=>'text','maxlength'=>'2','onKeyPress'=>'return numbersonly(this, event, false)');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="help.php?help=sCode" name="<?= _LBL_HELP_STATE_CODE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE_NAME ?></td>
			<td><?php
				$param1 = 'stateName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$stateName;
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'stateList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
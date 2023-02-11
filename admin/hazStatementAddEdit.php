<?php
	//****************************************************************/
	// filename: hazStatementAddEdit.php
	// description: add & edit the hazard statement
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
				// $sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config['includes_path']."javascript/ext-core/ext-core.js,". // javascript
				$sys_config['includes_path']."javascript/elastic-textarea.js,". // javascript
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
	if(isset($_POST['statementID'])) $statementID = $_POST['statementID'];
	elseif(isset($_GET['statementID'])) $statementID = $_GET['statementID'];
	else $statementID = 0;
	//==========================================================
	if($statementID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$statementCode = isset($_POST['statementCode'])?'H'.$_POST['statementCode']:'';
		$statementDesc = isset($_POST['statementDesc'])?$_POST['statementDesc']:'';
		$typeID = isset($_POST['typeID'])?$_POST['typeID']:'';
		$active = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		$param1 = 'tbl_Hazard_Statement';
		$param2 = array('AND Statement_Code'=>"= ".quote_smart($statementCode), 'AND Statement_ID'=>" != ".quote_smart($statementID), 'AND isDeleted'=>"= 0");
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
			if($statementID!=0){
				//==========================================================
				$sqlUpd	= "UPDATE tbl_Hazard_Statement SET"
						. " Statement_Code = ".quote_smart($statementCode).","
						. " Statement_Desc = ".quote_smart($statementDesc).","
						. " Type_ID = ".quote_smart($typeID).","
						. " UpdateBy = ".quote_smart($updBy)."," 
						. " UpdateDate = NOW()," 
						. " Active = ".quote_smart($active).""
						. " WHERE Statement_ID = ".quote_smart($statementID)." LIMIT 1";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
				//==========================================================
			}else{
				//==========================================================
				$sqlIns	= "INSERT INTO tbl_Hazard_Statement ("
						. " Statement_Code, Statement_Desc, Type_ID,"
						. " UpdateBy, UpdateDate,"
						. " RecordBy, RecordDate,"
						. " Active" 
						. " ) VALUES ("
						. " ".quote_smart($statementCode).",".quote_smart($statementDesc).",".quote_smart($typeID).","
						. " ".quote_smart($updBy).", NOW(),"
						. " ".quote_smart($updBy).", NOW(),"
						. " ".quote_smart($active).""
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$statementID = $db->sql_nextid();
				//==========================================================
				$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully'; 
				//==========================================================
			}
			//==========================================================
			func_add_audittrail($updBy,$msg.' ['.$statementID.']','Admin-Hazard Statement','tbl_Hazard_Statement,'.$statementID);
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				location.href="hazStatementList.php";
				</script>
				';
			exit();
		}
	}
	//==========================================================
	$statementCode = '';
	$statementDesc = '';
	$typeID = 0;
	$active = 1;
	//==========================================================
	if($statementID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Hazard_Statement';
		$dataWhere = array("AND Statement_ID" => "= ".quote_smart($statementID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere); 
		//==========================================================
		foreach($arrayData as $detail){
			$statementCode = substr($detail['Statement_Code'],1);
			$statementDesc = $detail['Statement_Desc'];
			$typeID = $detail['Type_ID'];
			$active = $detail['Active'];
		}
		//==========================================================
	}
	//==========================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $('input[title]').each(function() {
			$(this).qtip({
				//content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name:'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		}); */
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_HAZARD_STATEMENT .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('statementID',$statementID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td width="80%"><label>H</label> <?php
				$param1 = 'statementCode';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$statementCode;
				$param3 = array('type'=>'text','maxlength'=>'3',
								'onKeyPress'=>'return numbersonly(this, event, false)','style'=>'width:50px',
								'title'=>'Maximum character is 3.<br />Only number was accepted',
						);
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATEMENT_DESC ?></td>
			<td><?php
				$param1 = 'statementDesc';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$statementDesc;
				$param3 = array('cols'=>'50');
				echo form_textarea($param1,$param2,$param3);
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'hazStatementList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<script type="text/javascript">
		elasticTextArea("statementDesc");
	</script>
	</form>
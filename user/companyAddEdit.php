<?php
	//****************************************************************/
	// filename: companyAddEdit.php
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
				"", // css
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	//==========================================================
	if(isset($_POST['hidrow'])) $hidrow = $_POST['hidrow'];
	else $hidrow = 1;
	//==========================================================
	if(isset($_POST['newhidrow'])) $newhidrow = $_POST['newhidrow'];
	else $newhidrow = 1;
	//==================================================================================================================
	if(isset($_POST['save'])){
		$updBy = $Usr_ID;
		$success = 1;
		$msg = '';
		//=====================================================================
		$companyIDs = ''; 
		for($i=1;$i<$newhidrow;$i++){
			$companyID 		= isset($_POST['companyID'.$i])?$_POST['companyID'.$i]:'';
			$companyName 	= isset($_POST['companyName'.$i])?$_POST['companyName'.$i]:'';
			$active 	= isset($_POST['active'.$i])?$_POST['active'.$i]:'';
			//=====================================================================
			if($companyID!=''){
				if($companyName!=''){
					$sqlUpd	= "UPDATE tbl_Client_Company SET"
							. " Company_Name = ".quote_smart($companyName).","
							. " Active = ".quote_smart($active).","
							. " UpdateBy = ".quote_smart($updBy).","
							. " UpdateDate = NOW()"
							. " WHERE Company_ID = ".quote_smart($companyID)." LIMIT 1"
							;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					if($resUpd) $success++;
				}else{
					$sqlUpd	= "UPDATE tbl_Client_Company SET"
							//. " Synonym_Name = ".quote_smart($chemSyn).","
							. " UpdateBy = ".quote_smart($updBy).","
							. " UpdateDate = NOW(),"
							. " Active = 0,"
							. " isDeleted = 1"
							. " WHERE Company_ID = ".quote_smart($companyID)." LIMIT 1"
							;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					if($resUpd) $success++;
				}
				if($i>1){ $companyIDs .= ','; }
				$companyIDs .= $companyID;
			}else{
				if($companyName!=''){
					$sqlIns	= "INSERT INTO tbl_Client_Company ("
							. " Company_Name,Active,Usr_ID,"
							. " RecordBy,RecordDate,UpdateBy,UpdateDate"
							. " ) VALUES ("
							. " ".quote_smart($companyName).",".quote_smart($active).",".quote_smart($Usr_ID).","
							. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW()"
							. " )";
					$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
					$companyID = $db->sql_nextid();
					if($resIns) $success++;
					//=============================
					if($i>1){ $companyIDs .= ','; }
					$companyIDs .= $companyID;
				}else{ $success++; }
			}
		}
		//=====================================================================
		if($i==$success){ $msg = 'Insert/Update (All) Record(s) Successfully.'; }
		else { $msg = 'Insert/Update (Partial) Record(s) Successfully.'; }
		func_add_audittrail($updBy,$msg.' ['.$companyIDs.']','User-Client Company','tbl_Client_Company,'.$companyIDs);
		//=====================================================================
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="companyAddEdit.php";
			</script>
			';	
	}
	//==================================================================================================================
	if($Usr_ID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Client_Company';
		$dataJoin = '';
		$dataWhere = array("AND Usr_ID" => "= ".quote_smart($Usr_ID),"AND isDeleted" => "= 0");
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
	}
	//==================================================================================================================
?>
	<script language="Javascript">
	function addRow(){
		var d = document;
		var row = d.getElementById('hidrow').value;
		d.getElementById('hidrow').value = parseInt(row) + 1;
		d.myForm.submit();
	}
	function emptyRow(el){
		var d = document;
		d.getElementById(el).value = '';
	}
	$(document).ready(function() {
		$('img[title]').each(function() {
			$(this).qtip({
				//content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
	});
	</script>
	<?php func_window_open2(_LBL_CHEMICAL_USERS); ?>
	<form name="myForm" action="" method="post">

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="3" style="vertical-align:middle"><?= _LBL_CHEMICAL_USERS ?> <a href="#" onClick="addRow();"><?= _LBL_ADD ?></a></td>
		</tr>
		<?php 
			$i = 1;
			foreach($arrayData as $detail){
				echo form_input('companyID'.$i,$detail['Company_ID'],array('type'=>'hidden'));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY ." ". $i ?></td>
			<td width="70%"><?php
				//=======================================
				$param1 = 'companyName'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detail['Company_Name'];
				$param3 = array('type'=>'text','style'=>'width:300px');
				echo form_input($param1,$param2,$param3);
				//=======================================
				$emptyParam = $param1;
				//=======================================
				echo '&nbsp;';
				//=======================================
				$param1 = 'active'.$i;
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$detail['Active'];
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
				//=======================================
			?></td>
			<td width="10%"><?php
				$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
				$imgParam2 = _LBL_EMPTY;
				$imgParam3 = 'emptyRow(\''.$emptyParam.'\');';
				echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
			?>
			</td>
		</tr>
		<?php
				$i++;
			}
			echo form_input('hidrow',$hidrow,array('type'=>'hidden'));
			if($i==0) $i++;
			else $newhidrow = $hidrow+$i;
			for(;$i<$newhidrow;$i++){
				echo form_input('companyID'.$i,'',array('type'=>'hidden'));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY ." ". $i ?></td>
			<td width="70%"><?php
				$param1 = 'companyName'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','style'=>'width:300px');
				echo form_input($param1,$param2,$param3);
				//=======================================
				$emptyParam = $param1;
				//=======================================
				echo '&nbsp;';
				//=======================================
				$param1 = 'active'.$i;
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:'1';
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
				//=======================================
			?></td>
			<td width="10%"><?php
				$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
				$imgParam2 = _LBL_EMPTY;
				$imgParam3 = 'emptyRow(\''.$emptyParam.'\');';
				echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
			?>
			</td>
		</tr>
		<?php 
			}
			echo form_input('newhidrow',$newhidrow,array('type'=>'hidden'));
		?>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//=======================================
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				//=======================================
				echo '&nbsp;';
				//=======================================
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'companyAddEdit.php\');');
				echo form_button($param1,$param2,$param3);
				//=======================================
			?></td>
		</tr>
	</table>
	<br />
	</form>
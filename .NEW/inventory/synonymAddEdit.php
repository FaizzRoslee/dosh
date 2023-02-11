<?php
	//****************************************************************/
	// filename: synonymAddEdit.php
	// description: add/edit the details of chemical synonym
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
				$sys_config["includes_path"]."css/global.css,". // css	
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
	if(isset($_POST["chemicalID"])) $chemicalID = $_POST["chemicalID"];
	elseif(isset($_GET["chemicalID"])) $chemicalID = $_GET["chemicalID"];
	else $chemicalID = 0;
	/*********/
	if(isset($_POST["hidrow"])) $hidrow = $_POST["hidrow"];
	else $hidrow = 1;
	/*********/
	if(isset($_POST["newhidrow"])) $newhidrow = $_POST["newhidrow"];
	else $newhidrow = 1;
	/*********/
	if($chemicalID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	if(isset($_POST["save"])){
		$updBy = $_SESSION["user"]["Usr_ID"];
		$success = 1;
		$msg = "";
		/*********/
		$chemSynIDs = ""; 
		for($i=1;$i<$newhidrow;$i++){
			$chemSynID 	= isset($_POST["chemSynID".$i])?$_POST["chemSynID".$i]:"";
			$chemSyn 	= isset($_POST["chemSyn".$i])?$_POST["chemSyn".$i]:"";
			/*********/
			if($chemSynID!=""){
				if($chemSyn!=""){
					/*********/
					$sqlUpd	= "UPDATE tbl_Chemical_Synonyms SET"
							. " Synonym_Name = ".quote_smart($chemSyn).","
							. " UpdateBy = ".quote_smart($updBy).","
							. " UpdateDate = NOW()"
							. " WHERE Synonym_ID = ".quote_smart($chemSynID)." AND Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1"
							;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					if($resUpd) $success++;
					/*********/
				}else{
					/*********/
					$sqlUpd	= "UPDATE tbl_Chemical_Synonyms SET"
							. " UpdateBy = ".quote_smart($updBy).","
							. " UpdateDate = NOW(),"
							. " Active = 0"
							. " WHERE Synonym_ID = ".quote_smart($chemSynID)." AND Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1"
							;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					if($resUpd) $success++;
					/*********/
				}
				/*********/
				if($i>1){ $chemSynIDs .= ','; }
				$chemSynIDs .= $chemSynID;
				/*********/
			}else{
				if($chemSyn!=''){
					$sqlIns	= "INSERT INTO tbl_Chemical_Synonyms ("
							. " Synonym_Name,Chemical_ID,"
							. " RecordBy,RecordDate,UpdateBy,UpdateDate"
							. " ) VALUES ("
							. " ".quote_smart($chemSyn).",".quote_smart($chemicalID).","
							. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW()"
							. " )";
					$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
					$chemSynID = $db->sql_nextid();
					if($resIns) $success++;
					/*********/
					if($i>1){ $chemSynIDs .= ','; }
					$chemSynIDs .= $chemSynID;
					/*********/
				}else{ $success++; }
			}
		}
		/*********/
		if($i==$success){ $msg = "Insert/Update (All) Record(s) Successfully."; }
		else { $msg = "Insert/Update (Partial) Record(s) Successfully."; }
		func_add_audittrail($updBy,$msg." [".$chemSynIDs."]","Inventory-Chemical Synonynms","tbl_Chemical_Synonyms,".$chemSynIDs);
		/*********/
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="synonymList.php";
			</script>
			';	
		exit();
	}
	/*********/
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Chemical c";
		$dataJoin = "";
		$dataWhere = array("AND c.Chemical_ID" => " = ".quote_smart($chemicalID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
		}
	}
	/*********/
	$str_onClick = "getChemID();";
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
	function getChemID(){
		url = '<?= $sys_config['inventory_path'] ?>chemicalSearch.php';
		funcPopup(url,'900','600');
	}
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_SYNONYM .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('chemicalID',$chemicalID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemName;
				$param3 = array('type'=>'text','readonly'=>'readonly','style'=>'width:50%;','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3); ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemCAS';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemCAS;
				$param3 = array('type'=>'text','readonly'=>'readonly','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
	</table>
<!--
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?= $chemName; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?php
				$findDash = strpbrk($chemCAS,'-');
				if($findDash==TRUE){
					$ex_cas = explode('-',$chemCAS);
					$chemCAS1 = $ex_cas[0];
					$chemCAS2 = $ex_cas[1];
					$chemCAS3 = $ex_cas[2];
					echo $chemCAS1.' - '.$chemCAS2.' - '.$chemCAS3;
				}else{
					$chemCAS1 = '';
					$chemCAS2 = '';
					$chemCAS3 = '';
					echo '-';
				}
			?></td>
		</tr>
	</table>
-->
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="30">
			<td class="label title" colspan="4"><?= _LBL_SYNONYM_NAME ?> <a href="#" onClick="addRow();"><?= _LBL_ADD ?></a></td>
		</tr>
		<?php 
			$param1 = 'Synonym_ID,Synonym_Name';
			$param2 = 'tbl_Chemical_Synonyms';
			$param3 = '';
			$param4	= array("AND Chemical_ID" => " = ".quote_smart($chemicalID),"AND Active" => "= 1");
			$arrSynonym = _get_arrayData($param1,$param2,$param3,$param4);
			//print_r($arrSynonym);
			$i = 1;
			foreach($arrSynonym as $detail){
				echo form_input('chemSynID'.$i,$detail['Synonym_ID'],array('type'=>'hidden'));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><?php
				$param1 = 'chemSyn'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detail['Synonym_Name'];
				$param3 = array('type'=>'text','style'=>'width:300px');
				echo form_input($param1,$param2,$param3);
				
				$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
				$imgParam2 = _LBL_EMPTY;
				$imgParam3 = 'emptyRow(\''.$param1.'\');';
				echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
			?></td>
		</tr>
		<?php
				$i++;
			}
			echo form_input('hidrow',$hidrow,array('type'=>'hidden'));
			if($i==0) $i++;
			else $newhidrow = $hidrow+$i;
			for(;$i<$newhidrow;$i++){
				echo form_input('chemSynID'.$i,'',array('type'=>'hidden'));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><?php
				$param1 = 'chemSyn'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','style'=>'width:300px');
				echo form_input($param1,$param2,$param3);
				
				$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
				$imgParam2 = _LBL_EMPTY;
				$imgParam3 = 'emptyRow(\''.$param1.'\');';
				echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
			?></td>
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
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'synonymList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
<?php
	//****************************************************************/
	// filename: synonymView.php
	// description: view the synonym details
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
	if($userLevelID>=6) exit();
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
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Chemical c";
		$dataJoin = "";
		$dataWhere = array("AND c.Chemical_ID" => "= ".quote_smart($chemicalID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*********/
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
		var row = d.getElementById("hidrow").value;
		d.getElementById("hidrow").value = parseInt(row) + 1;
		d.myForm.submit();
	}
	function emptyRow(el){
		var d = document;
		d.getElementById(el).value = "";
	}
	function getChemID(){
		url = "<?= $sys_config["inventory_path"] ?>chemicalSearch.php";
		funcPopup(url,"700","600");
	}
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_SYNONYM .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('chemicalID',$chemicalID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?= $chemName; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?= $chemCAS; ?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="30">
			<td class="label title" colspan="4"><?= _LBL_SYNONYM_NAME ?></td>
		</tr>
		<?php 
			$param1 = 'Synonym_ID,Synonym_Name';
			$param2 = 'tbl_Chemical_Synonyms';
			$param3 = '';
			$param4	= array("AND Chemical_ID"=>"= ".quote_smart($chemicalID),"AND Active"=>"= 1");
			$arrSynonym = _get_arrayData($param1,$param2,$param3,$param4);
			//print_r($arrSynonym);
			$i = 1;
			foreach($arrSynonym as $detail){
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><label><?= $detail['Synonym_Name']; ?></label></td>
		</tr>
		<?php
				$i++;
			}
			if($i==1){
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><label><?= '-'; ?></label></td>
		</tr>
		<?php 
			}
		?>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				if($userLevelID<=2){
					$param3 = array("type"=>"button","onClick"=>"redirectForm('synonymAddEdit.php?chemicalID=".$chemicalID."');");
					echo form_button("edit",_LBL_EDIT,$param3);
					/*********/
					echo "&nbsp;";
				}
				/*********/
				$param3 = array("type"=>"button","onClick"=>"redirectForm('synonymList.php');");
				echo form_button("cancel",_LBL_BACK,$param3);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
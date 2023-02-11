<?php
	//****************************************************************/
	// filename: reportSubmission.php
	// description: list of the submission reports
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*****/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*****/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*****/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
	func_header("",
				$sys_config["includes_path"]."css/global.css,". // css	
				"", // css	
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*****/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*****/
	// if(isset($_POST["rptType"])) $rptType = $_POST["rptType"];
	// elseif(isset($_GET["rptType"])) $rptType = $_GET["rptType"];
	// else $rptType = "";
	$rptType = "rpt03";
	/*****/
	if(isset($_POST["chemType"])) $chemType = $_POST["chemType"];
	elseif(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*****/
	if(isset($_POST["status"])) $status = $_POST["status"];
	elseif(isset($_GET["status"])) $status = $_GET["status"];
	else $status = "";
	/*****/
	if(isset($_POST["rptYear"])) $rptYear = $_POST["rptYear"];
	elseif(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = date("Y");
	/*****/
	if(isset($_POST['send'])){
		$fileRpt = 'report03_supp.php';
		/*****/
		$url = $fileRpt.'?rptType='.$rptType.'&chemType='.$chemType.'&status='.$status.'&rptYear='.$rptYear;
		echo '
			<script language="Javascript">
			funcPopup("'.$url.'","1100","800");
			</script>
			';
	}
	/*****/
?>
	<script language="Javascript">
	function showHide(){
		var d = document;
		var status = d.getElementById("status").value;
		
		if(status!=""||status!=0)
			d.getElementById("send").style.display = "";
		else
			d.getElementById("send").style.display = "none";
	}
	function changeValue(){
		var d = document;
		d.getElementById("status").value = "";
		d.myForm.submit();
	}
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
		
	});
	</script>
	<?php func_window_open2( _LBL_REPORT .' :: '._LBL_SUBMISSION); ?>
	<form name="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<?php
		/*
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REPORT_TYPE ?></td>
			<td width="80%"><?php
				$param1 = 'rptType';
				$param2 = '';
				$param3 = $arrReportSub;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptType;
				//$param5 = array('onChange'=>'document.myForm.submit();');
				$param5 = array('onChange'=>'changeValue();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
				$rType = $param4;
			?></td>
		</tr>
		*/
			$rType = $rptType;
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
			<td width="80%"><?php
				$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
				$param1 = 'chemType';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Type','Type_ID',$colTypeName,array('AND Active'=>'= 1'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$chemType;
				$param5 = array('onChange'=>'showHide();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents" style="display:none;">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
			/*
				$param1 = 'status';
				$param2 = '';
				if($rType=='rpt03') $param3 = $arrRptStatusN;
				elseif($rType=='rpt04') $param3 = $arrRptStatusR;
				else $param3 = array();
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$status;
				$param5 = array('onChange'=>'showHide();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
			*/
				$dOptions = array("type"=>"hidden");
				echo _LBL_APPROVED . form_input("status",31,$dOptions);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YEAR ?></td>
			<td><?php
				/*****/
				$sql	= "SELECT YEAR(ApprovedDate) AS `year` FROM tbl_Submission"
						. " WHERE 1 AND ApprovedDate IS NOT NULL AND Status_ID IN (31,32,33,41,42,43)"
						. " GROUP BY `year`";
				$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*****/
				$arr_options = array();
				while($row = $db->sql_fetchrow($res)){
					$value = $row["year"];
					$arr_options[$value]["label"] = $value-1;
					$arr_options[$value]["value"] = $value;
				}
				/*****/
				$param1 = "rptYear";
				$param2 = "";
				$param3 = $arr_options;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptYear;
				$param5 = "";
				echo form_select($param1,$param2,$param3,$param4,$param5);
				/*****/
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				if($status!=""||$status!=0) $display = "";
				else $display = "none";
				/*****/
				$param3 = array("type"=>"submit","style"=>"display:".$display);
				echo form_button("send",_LBL_SUBMIT,$param3);
				/*****/
				echo "&nbsp;";
				/*****/
				$param3 = array("type"=>"button","onClick"=>"redirectForm('reportSubmission_supp.php');");
				echo form_button("back",_LBL_CANCEL,$param3);
				/*****/
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
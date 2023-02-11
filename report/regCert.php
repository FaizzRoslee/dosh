<?php
	//****************************************************************/
	// filename: regCert.php
	// description: overall summary report
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
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
	func_header("",
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
	if(isset($_POST["Usr_ID"])) $Usr_ID = $_POST["Usr_ID"];
	elseif(isset($_GET["Usr_ID"])) $Usr_ID = $_GET["Usr_ID"];
	else $Usr_ID = 0;
	/*****/	
	if(in_array($_SESSION["user"]["Level_ID"],array("6","7","8"))){
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
	}
	/*****/	
	$regID 	= "";
	$regDate = "";
	$actDate = "";
	$coName	= "";
	$coNo	= "";
	$lvlName = "";
	/*****/
	if($Usr_ID!=0){
		$dataColumns = "u.*, uc.*";
		$dataTable = "sys_User u";
		$dataJoin = array("sys_User_Client uc"=>"u.Usr_ID = uc.Usr_ID");
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*****/
		foreach($arrayData as $detail){
			$regID 	= strtoupper($detail["Reg_ID"]);
			$regDate = func_ymd2dmy($detail["RecordDate"]);
			$actDate = func_ymd2dmy($detail["ActivationDate"]);
			$coName	= strtoupper($detail["Company_Name"]);
			$coNo	= strtoupper($detail["Company_No"]);
			$lvlID = $detail["Level_ID"];
		}
	}
	/*****/
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_CERTIFICATION); ?>
	<center>
	<table border="0"  width="80%" cellspacing="0" cellpadding="2">
		<tr><td width="100%" style="text-align:right"><i>CIMS/CRT/01</i></td></tr>
	</table>
	<table width="80%">
		<tr>
			<td align="center"><?php
				echo '<img src="'.$sys_config['images_path'].'jata.gif" />';
				echo '<br /><br />';
				echo strtoupper(_LBL_DOSH);
				echo '<br /><br />';
				echo strtoupper(_LBL_CIMS);
				echo '<br /><br />';
				echo strtoupper(_LBL_CERT_TITLE);
			?></td>
		</tr>
	</table>
	<br />
	<table style="border:solid" cellspacing="1" cellpadding="3" width="80%">
		<tr>
			<td align="center"><table cellspacing="1" cellpadding="10" width="90%">
				<tr>
					<td width="30%"><label><strong><?= _LBL_CERT_REG_ID ?></strong></label></td>
					<td width="70%">: <label><?= $regID ?></label></td>
				</tr>
				<tr>
					<td><label><strong><?= _LBL_CERT_REG_DATE ?></strong></label></td>
					<td>: <label><?= $regDate ?></label></td>
				</tr>
				<tr>
					<td><label><strong><?= _LBL_CERT_ACTIVATION_DATE ?></strong></label></td>
					<td>: <label><?= $actDate ?></label></td>
				</tr>
				<tr>
					<td><label><strong><?= _LBL_CERT_COMP_NAME ?></strong></label></td>
					<td>: <label><?= $coName ?></label></td>
				</tr>
				<tr>
					<td><label><strong><?= _LBL_CERT_COMP_REG_NO ?></strong></label></td>
					<td>: <label><?= $coNo ?></label></td>
				</tr>
				<tr>
					<td><label><strong><?= _LBL_CERT_TYPE_SUPPIER ?></strong></label></td>
					<td>: <label><?php
						foreach($arrLevel as $detail){
							if($detail['value']==$lvlID){
								echo $detail['label']; 
								break; 
							}
						}
					?></label></td>
				</tr>
				<tr>
					<td colspan="2"><hr size="3" color="black" /></td>
				</tr>
				<tr>
					<td colspan="2"><label><?= _LBL_CERT_SUBJ_INFO . _LBL_CERT_ORG_INFO ?></label></td>
				</tr>
			</table></td>
		</tr>
	</table>
	<br />
	<table width="100%">
		<tr>
			<td align="center"><?php
				//=========================================
				$url_pdf = 'regCert_pdf.php?Usr_ID='.$Usr_ID;
				echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."','1000','800');"));
				//=========================================
				//echo '&nbsp;';
				//=========================================
				//echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
				//=========================================
			?></td>
		</tr>
	</table>
	</center>
	<?php func_window_close2(); ?>

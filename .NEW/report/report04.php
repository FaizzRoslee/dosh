<?php
	//****************************************************************/
	// filename: report03.php
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
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
	$uLevelID = $_SESSION["user"]["Level_ID"];
	// if($uLevelID==5) exit();
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
	if(isset($_GET["lvlType"])) $lvlType = $_GET["lvlType"];
	else $lvlType = "";
	/*****/
	if(isset($_GET["state"])) $state = $_GET["state"];
	else $state = "";
	/*****/
	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
		}
		$lvlName = substr_replace($lvlName,"",-2);
		$levelType = "6,7,8";
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		$levelType = $lvlType;
	}
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php $text_align_center = "text-align:center;"; ?>
	<?php $text_align_right = "text-align:right;"; ?>
	<?php $text_valign_mid = "vertical-align:middle;"; ?>
	<table width="100%">
		<tr>
			<td align="center"><?php
				/*****/
				echo '<img src="'.$sys_config["images_path"].'jata.gif" />';
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_DOSH);
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_CIMS);
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_REPORT) .": "; echo strtoupper(_LBL_LIST_SUPPLIER);
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_SUPPLIER_TYPE) .": "; echo ($lvlName!="") ? strtoupper($lvlName) : "-";
				/*****/
			?></td>
		</tr>
	</table>
	<br />
	<?php 
		$showExport = 0;
	?>
	
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<thead>
		<tr class="contents">
			<td width="4%" class="label" style="<?= $text_align_center ?>"><?= _LBL_BIL ?></td>
			<td width="25%" class="label" style="<?= $text_align_center ?>"><?= _LBL_SUPPLIER_NAME ?></td>
			<td width="40%" class="label" style="<?= $text_align_center ?>"><?= _LBL_ADDRESS ?></td>
			<td width="31%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CONTACT_DETAIL ?></td>
		</tr>
		</thead>
		<tbody>
		<?php
		
			$arr_state = explode(',',$state);
			foreach($arr_state as $value){
		?>
		<tr class="contents">
			<td colspan="4" class="label"><?= _get_StrFromCondition("sys_State","State_Name","State_ID",$value); ?></td>
		</tr>
		<?php
				/*****/
				$dataColumns = "u.Usr_ID,u.Level_ID,u.Active,uc.Company_Name,uc.Address_Reg,uc.Postcode_Reg,uc.City_Reg,"
							. "uc.Contact_Name,uc.Contact_Designation,uc.Contact_Mobile_No,uc.Contact_Email,Level_ID";
				$dataTable = "sys_User_Client uc";
				$dataJoin = array("sys_User u"=>"uc.Usr_ID = u.Usr_ID");
				$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
				$dataWhere["AND u.Active"] = "= 1";
				$dataWhere["AND u.isDeleted"] = "= 0";
				if($lvlType!="")
					$dataWhere["AND Level_ID"] = "IN (". quote_smart($levelType,false) .")";
				$dataOrder = "Level_ID ASC, Company_Name ASC";
				$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
				
				// echo "<pre>";
				// print_r($arrayData);
				// echo "</pre>";
				/*****/
				$bil = 0;
				foreach($arrayData as $data){
					$bil++;
					$showExport++;
					
					$add1 		= $data['Address_Reg'];
					$add2 		= $data['Postcode_Reg'];
					$add3 		= $data['City_Reg'];
					$compAdd	= nl2br($add1) . "<br />$add2 $add3";
					$contact	= _LBL_NAME .": ". strtoupper($data["Contact_Name"]) ."<br />"
								. _LBL_CONTACT_NO .": ". $data["Contact_Mobile_No"] ."<br />"
								. _LBL_EMAIL .": ". $data["Contact_Email"];
					
					$lvlID = $data["Level_ID"];
					$lvlName = "";
					if($lvlType=="all")		
						$lvlName = "<br /><b>[". _getLabel($lvlID,$arrLevel) ."]</b>";
		?>
		<tr class="contents">
			<td width="4%" style="<?= $text_align_center.$text_valign_mid ?>"><?= $bil ?></td>
			<td width="25%" style="<?= $text_valign_mid ?>"><?= strtoupper($data["Company_Name"]) ."$lvlName" ?></td>
			<td width="40%" style="<?= $text_valign_mid ?>"><?= $compAdd ?></td>
			<td width="31%" style="<?= $text_valign_mid ?>"><?= $contact ?></td>
		</tr>
		<?php
				}
				if($bil==0){
		?>
		<tr class="contents">
			<td colspan="4" style="<?= $text_align_center.$text_valign_mid ?>"><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
				}
			}
		?>
		</tbody>
	</table>
	<br />
	<table width="100%">
		<tr>
			<td align="center"><?php
				if($showExport>0){
					$url_pdf = 'report04_to_pdf.php?lvlType='.$lvlType.'&state='.$state;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>

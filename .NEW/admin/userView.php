<?php
	//****************************************************************/
	// filename: userView.php
	// description: show details of the user
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
				$sys_config["includes_path"]."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config["includes_path"]."css/global.css,". // css	
				"",
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
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
	/*********/
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID>2) exit();
	/*********/
	if(isset($_POST["Usr_ID"])) $Usr_ID = $_POST["Usr_ID"];
	elseif(isset($_GET["Usr_ID"])) $Usr_ID = $_GET["Usr_ID"];
	else $Usr_ID = 0;
	/*********/
	if(isset($_POST["levelID"])) $levelID = $_POST["levelID"];
	elseif(isset($_GET["levelID"])) $levelID = $_GET["levelID"];
	else $levelID = 6;
	/*********/
	$regID 		= "";
	$regNo	 		= "";
	$companyNo 		= "";
	$companyName 	= "";
	$address 	 	= "";
	$city	 		= "";
	$postcode 		= "";
	$state			= "";
	$p_address 	 	= "";
	$p_city	 		= "";
	$p_postcode 	= "";
	$p_state		= "";
	$contactCode	= "";
	$phoneNo		= "";
	$faxNo			= "";
	$email			= "";
	/*********/
	$c_name			= "";
	$c_designation	= "";
	$c_mobile		= "";
	$c_email		= "";
	/*********/
	$userid		= "";
	// $pswd		= "";
	// $r_pswd		= "";
	/*********/
	$security_Q		= "";
	$answer			= "";
	$active			= 1;
	//$securityCode	= "";
	/*********/
	if($Usr_ID!=0){
		$dataColumns = "*";
		$dataTable = "sys_User u";
		$dataJoin = array("sys_User_Client uc"=>"u.Usr_ID = uc.Usr_ID");
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$regID 			= $detail["Reg_ID"];
			$regNo	 		= $detail["Reg_No"];
			$companyNo 		= $detail["Company_No"];
			$companyName 	= $detail["Company_Name"];
			$address 	 	= $detail["Address_Reg"];
			$city	 		= $detail["City_Reg"];
			$postcode 		= $detail["Postcode_Reg"];
			$state			= $detail["State_Reg"];
			$p_address 	 	= $detail["Address_Postal"];
			$p_city	 		= $detail["City_Postal"];
			$p_postcode 	= $detail["Postcode_Postal"];
			$p_state		= $detail["State_Postal"];
			$contactCode	= "";
			$phoneNo		= $detail["Phone_No"];
			$faxNo			= $detail["Fax_No"];
			$email			= $detail["Email"];
			/*********/
			$c_name			= $detail["Contact_Name"];
			$c_designation	= $detail["Contact_Designation"];
			$c_mobile		= $detail["Contact_Mobile_No"];
			$c_email		= $detail["Contact_Email"];
			/*********/
			$userid		= $detail["Usr_LoginID"];
			//$pswd		= $detail["Usr_Password"];
			//$r_pswd	= $detail["Usr_Password"];
			/*********/
			$security_Q		= $detail["Question_ID"];
			$answer			= $detail["Question_Answer"];
			//$securityCode	= "";
			$active		= $detail["Active"];
		}
	}
	//==========================================================
	$legendStyle = "font-weight:bold;font-size:12px;";
	$requiredField = '<span style="color:red"> * </span>';
?>
	<style type="text/css" title="currentStyle">
		.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="javascript">
	jQuery(document).ready(function() {
		var $tabs = $("#tabs").tabs({ selected: 2 });
		// var tab_selected = $tabs.tabs('option', 'selected'); // => 0 ...retrieve the index of the currently selected tab
		/* $("button").removeClass("btn-new-style").button(); */
	});
	
	function showButton(val){
		if(val==1){
			document.getElementById("spanEdit").style.display = '';
		}else{
			document.getElementById("spanEdit").style.display = 'none';
		}
	}
	</script>
	<?php
		$lvlName = '';
		foreach($arrLevel as $detail){
			if($detail['value']==$levelID){
				$lvlName = $detail['label']; 
				break; 
			}
		}	
	?>
	<?php func_window_open2(_LBL_USER_VIEW .' ['. $lvlName .']'); ?>
    <form name="myForm" id="myForm" action="" method="post">
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1" onClick="showButton(1)"><?= _LBL_DETAIL ?></a></li>
			<!--<li><a href="#tabs-2" onClick="showButton(0)"><?= _LBL_CHEMICAL_USERS ?></a></li>-->
		</ul>
		
		<div id="tabs-1">
		
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_ID ?></td>
			<td colspan="3"><label><?= ($regID!='')?$regID:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_NO ?></td>
			<td colspan="3"><label><?= ($regNo!='')?$regNo:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY_NO ?></td>
			<td colspan="3"><label><?= ($companyNo!='')?$companyNo:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_COMPANY_NAME ?></td>
			<td colspan="3"><label><?= ($companyName!='')?$companyName:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_REG_ADDRESS ?></td>
			<td width="30%" rowspan="3"><label><?= ($address!='')?nl2br($address):'-'; ?></label></td>
			<td class="label" width="20%"><?= _LBL_CITY ?></td>
			<td width="30%"><label><?= ($city!='')?$city:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE ?></td>
			<td><label><?= ($postcode!='')?$postcode:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><label><?= ($state!='')?_get_StrFromCondition('sys_State','State_Name','State_ID',$state):'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_POSTAL_ADDRESS ?></td>
			<td width="30%" rowspan="3"><label><?= ($p_address!='')?nl2br($p_address):'-'; ?></label></td>
			<td class="label"><?= _LBL_CITY ?></td>
			<td><label><?= ($p_city!='')?$p_city:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE ?></td>
			<td><label><?= ($p_postcode!='')?$p_postcode:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><label><?= ($p_state!='')?_get_StrFromCondition('sys_State','State_Name','State_ID',$p_state):'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHONE_NO ?></td>
			<td colspan="3"><label><?= ($phoneNo!='')?$phoneNo:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_FAX_NO ?></td>
			<td colspan="3"><label><?= ($faxNo!='')?$faxNo:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td colspan="3"><label><?= ($email!='')?$email:'-'; ?></label></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_CONTACT_PERSON ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME ?></td>
			<td colspan="3"><label><?= ($c_name!='')?$c_name:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESIGNATION ?></td>
			<td colspan="3"><label><?= ($c_designation!='')?$c_designation:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_MOBILE_NO ?></td>
			<td colspan="3"><label><?= ($c_mobile!='')?$c_mobile:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td colspan="3"><label><?= ($c_email!='')?$c_email:'-'; ?></label></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_USER_ID_PASSWORD ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_USERID ?></td>
			<td colspan="3"><label><?= ($userid!='')?$userid:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PASSWORD ?></td>
			<td colspan="3"><label><?= 'Not Shown' ?></label></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_ANSWER_IF_FORGOT_ID_PASSWORD ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SECURITY_QUESTION ?></td>
			<td colspan="3"><label><?= _get_StrFromCondition('sys_Security_Question','Question_Desc','Question_ID',$security_Q); ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YOUR_ANSWER ?></td>
			<td colspan="3"><label><?= ($answer!='')?$answer:'-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td colspan="3"><label><?= _getLabel($active,$arrStatusAktif) ?></label></td>
		</tr>
	</table>
		
		</div>
	</div>
	<br />
	<table class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				echo '<span id="spanEdit">';
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('userAddEdit.php?Usr_ID=".$Usr_ID."&levelID=".$levelID."');");
				echo form_button("edit",_LBL_EDIT,$dOthers);
				/*********/
				echo "&nbsp;";
				/*********/
				echo "</span>";	
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('userList.php?levelID=".$levelID."');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
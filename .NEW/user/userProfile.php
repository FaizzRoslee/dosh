<?php
	//****************************************************************/
	// filename: userAddEdit.php
	// description: detailed of the user
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
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	//==========================================================
	$regID	 		= '';
	$regNo	 		= '';
	$companyNo 		= '';
	$companyName 	= '';
	$address 	 	= '';
	$city	 		= '';
	$postcode 		= '';
	$state			= '';
	$p_address 	 	= '';
	$p_city	 		= '';
	$p_postcode 	= '';
	$p_state		= '';
	$contactCode	= '';
	$phoneNo		= '';
	$faxNo			= '';
	$email			= '';
	//======================
	$c_name			= '';
	$c_designation	= '';
	$c_mobile		= '';
	$c_email		= '';
	//======================
	$userid		= '';
	// $pswd		= '';
	// $r_pswd		= '';
	//======================
	$security_Q		= '';
	$answer			= '';
	$active			= 1;
	//$securityCode	= '';
	//==========================================================
	if($Usr_ID!=0){
		$dataColumns = '*';
		$dataTable = 'sys_User u';
		$dataJoin = array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//==========================================================
		foreach($arrayData as $detail){
			$regID	 		= $detail['Reg_ID'];
			$regNo	 		= $detail['Reg_No'];
			$companyNo 		= $detail['Company_No'];
			$companyName 	= $detail['Company_Name'];
			$address 	 	= $detail['Address_Reg'];
			$city	 		= $detail['City_Reg'];
			$postcode 		= $detail['Postcode_Reg'];
			$state			= $detail['State_Reg'];
			$p_address 	 	= $detail['Address_Postal'];
			$p_city	 		= $detail['City_Postal'];
			$p_postcode 	= $detail['Postcode_Postal'];
			$p_state		= $detail['State_Postal'];
			$contactCode	= '';
			$phoneNo		= $detail['Phone_No'];
			$faxNo			= $detail['Fax_No'];
			$email			= $detail['Email'];
			//======================
			$c_name			= $detail['Contact_Name'];
			$c_designation	= $detail['Contact_Designation'];
			$c_mobile		= $detail['Contact_Mobile_No'];
			$c_email		= $detail['Contact_Email'];
			//======================
			$userid		= $detail['Usr_LoginID'];
			//$pswd		= $detail['Usr_Password'];
			//$r_pswd	= $detail['Usr_Password'];
			//======================
			$security_Q		= $detail['Question_ID'];
			$answer			= $detail['Question_Answer'];
			//$securityCode	= '';
		}
	}
	
	$legendStyle = 'font-weight:bold;font-size:12px;';
	$requiredField = '<span style="color:red"> * </span>';
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_USER_PROFILE); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<div id="div_rel">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_LEVEL_TYPE ?></td>
			<td colspan="3"><label><strong><?php echo (isset($_SESSION['user']['Level_Name']) ? $_SESSION['user']['Level_Name']  : '') .' [' . _LBL_CONTACT_TO_CHANGE . _LBL_LEVEL_TYPE .']' ?></strong></label></td>
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
			<td colspan="3"><label><?= ($c_email!='')?$c_email:'-'; ?></label> <?= _LBL_EMEL_NOTE2 ?></td>
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
			<td colspan="3"><label><?= ($security_Q!=0) ? _get_StrFromCondition('sys_Security_Question','Question_Desc','Question_ID',$security_Q) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YOUR_ANSWER ?></td>
			<td colspan="3"><label><?= ($answer!='')?$answer:'-'; ?></label></td>
		</tr>
<!--
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td colspan="3"><?php
				$param1 = 'active';
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$active;
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
-->
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'edit';
				$param2 = _LBL_EDIT;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'userAddEdit.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	</div>
	<br />
	</form>
<?php	
	func_window_close2();
?>

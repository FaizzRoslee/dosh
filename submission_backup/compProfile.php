<?php
	//****************************************************************/
	// filename: compProfile.php
	// description: detailed of the company
	//****************************************************************/
	//include_once '../includes/sys_config.php';
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	$usrid = _get_StrFromCondition("tbl_Submission","Usr_ID","Submission_ID",$submissionID);
	$regId	 		= "";
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
	$phoneNo		= "";
	$faxNo			= "";
	$email			= "";
	/*********/
	$c_name			= "";
	$c_designation	= "";
	$c_mobile		= "";
	$c_email		= "";
	/*********/
	$userid			= "";
	/*********/
	$active			= "";
	/*********/
	if($usrid!=0){
		$dataColumns = "Reg_No,Reg_ID,Company_No,Company_Name,Address_Reg,City_Reg,Postcode_Reg,State_Reg,"
					.	"Address_Postal,City_Postal,Postcode_Postal,State_Postal,Phone_No,Fax_No,Email,"
					.	"Contact_Name,Contact_Designation,Contact_Mobile_No,Contact_Email,Usr_LoginID,Active";
		$dataTable = "sys_User u";
		$dataJoin = array("sys_User_Client uc"=>"u.Usr_ID = uc.Usr_ID");
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($usrid));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$regNo	 		= $detail["Reg_No"];
			$regId	 		= $detail["Reg_ID"];
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
			/*********/
			$active		= $detail["Active"];
		}
	}
	
	$legendStyle = "font-weight:bold;font-size:12px;";
	$requiredField = '<span style="color:red"> * </span>';
?>

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle;"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_NO ?></td>
			<td colspan="3"><label><?= ($regNo!="")?$regNo:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_ID ?></td>
			<td colspan="3"><label><?= ($regId!="")?$regId:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY_NO ?></td>
			<td colspan="3"><label><?= ($companyNo!="")?$companyNo:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_COMPANY_NAME ?></td>
			<td colspan="3"><label><?= ($companyName!="")?$companyName:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_REG_ADDRESS ?></td>
			<td width="30%" rowspan="3"><label><?= ($address!="")?nl2br($address):"-"; ?></label></td>
			<td class="label" width="20%"><?= _LBL_CITY ?></td>
			<td width="30%"><label><?= ($city!="")?$city:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE ?></td>
			<td><label><?= ($postcode!="")?$postcode:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><label><?= ($state!="")?_get_StrFromCondition("sys_State","State_Name","State_ID",$state):"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_POSTAL_ADDRESS ?></td>
			<td width="30%" rowspan="3"><label><?= ($p_address!="")?nl2br($p_address):"-"; ?></label></td>
			<td class="label"><?= _LBL_CITY ?></td>
			<td><label><?= ($p_city!="")?$p_city:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE ?></td>
			<td><label><?= ($p_postcode!="")?$p_postcode:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><label><?= ($p_state!="")?_get_StrFromCondition("sys_State","State_Name","State_ID",$p_state):"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHONE_NO ?></td>
			<td colspan="3"><label><?= ($phoneNo!="")?$phoneNo:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_FAX_NO ?></td>
			<td colspan="3"><label><?= ($faxNo!="")?$faxNo:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td colspan="3"><label><?= ($email!="")?$email:"-"; ?></label></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle;"><?= _LBL_CONTACT_PERSON ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME ?></td>
			<td colspan="3"><label><?= ($c_name!="")?$c_name:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESIGNATION ?></td>
			<td colspan="3"><label><?= ($c_designation!="")?$c_designation:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_MOBILE_NO ?></td>
			<td colspan="3"><label><?= ($c_mobile!="")?$c_mobile:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL ?></td>
			<td colspan="3"><label><?= ($c_email!="")?$c_email:"-"; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td colspan="3"><label><?php 
				foreach($arrStatusAktif as $detail){
					if($detail["value"]==$active) echo $detail["label"]; 
				}
			?></label></td>
		</tr>
	</table>


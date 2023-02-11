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
	if($Usr_ID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){			
		$regNo	 		= isset($_POST['regNo'])?$_POST['regNo']:'';
		$companyNo 		= isset($_POST['companyNo'])?$_POST['companyNo']:'';
		$companyName 	= isset($_POST['companyName'])?$_POST['companyName']:'';
		$address		= isset($_POST['address'])?$_POST['address']:'';
		$city			= isset($_POST['city'])?$_POST['city']:'';
		$postcode		= isset($_POST['postcode'])?$_POST['postcode']:'';
		$state			= isset($_POST['state'])?$_POST['state']:0;
		$p_address		= isset($_POST['p_address'])?$_POST['p_address']:'';
		$p_city			= isset($_POST['p_city'])?$_POST['p_city']:'';
		$p_postcode		= isset($_POST['p_postcode'])?$_POST['p_postcode']:'';
		$p_state		= isset($_POST['p_state'])?$_POST['p_state']:0;
		$phoneNo		= isset($_POST['phoneNo'])?$_POST['phoneNo']:'';
		$faxNo			= isset($_POST['faxNo'])?$_POST['faxNo']:'';
		$email			= isset($_POST['email'])?$_POST['email']:'';
		//===============================================================================
		$c_name			= isset($_POST['c_name'])?$_POST['c_name']:'';
		$c_designation	= isset($_POST['c_designation'])?$_POST['c_designation']:'';
		$c_mobile		= isset($_POST['c_mobile'])?$_POST['c_mobile']:'';
		$c_email		= isset($_POST['c_email'])?$_POST['c_email']:'';
		//===============================================================================
		$userid			= isset($_POST['userid'])?$_POST['userid']:'';
		$pswd			= isset($_POST['pswd'])?$_POST['pswd']:'';	
		$r_pswd			= isset($_POST['r_pswd'])?$_POST['r_pswd']:'';
		//===============================================================================
		$security_Q		= isset($_POST['security_Q'])?$_POST['security_Q']:0;
		$answer			= isset($_POST['answer'])?$_POST['answer']:'';
		//$active			= isset($_POST['active'])?$_POST['active']:0;
		//===============================================================================
		$updBy = $Usr_ID;
		//===============================================================================
		$checkLoginID	= _get_RowExist(
								'sys_User',
								array('AND Usr_LoginID'=>"= ".quote_smart($userid),'AND isDeleted'=>"= 0",'AND Usr_ID'=>"!= ".quote_smart($Usr_ID)),
								''
						);//$table='', $where=array(), $others=array()
		$checkCompNo	= _get_RowExist(
								'sys_User u',
								array('AND uc.Company_No'=>"= ".quote_smart($companyNo),'AND u.isDeleted'=>"= 0",'AND u.Usr_ID'=>"!= ".quote_smart($Usr_ID)),
								array('LEFT OUTER JOIN sys_User_Client uc ON u.Usr_ID = uc.Usr_ID')
						);//$table='', $where=array(), $others=array()
		//===============================================================================
		if($checkLoginID>0 || $checkCompNo>0){
			$msg = '';
			if($checkLoginID>0) $msg .= 'Login ID is not available\n';
			if($checkCompNo>0) $msg .= 'Company No already registered with the system\n';
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				</script>
				';
		}else{
			if($Usr_ID!=0){
				$sqlUpd	= "UPDATE sys_User SET"
						. " Usr_LoginID = ".quote_smart(strtoupper($userid)).","
						//. " Usr_Password = ".quote_smart($userid).","
						. " Level_ID = ".quote_smart($levelID).","
						. " UpdateBy = ".quote_smart($updBy).",UpdateDate = NOW()"
						//. " Active = ".quote_smart($active).""
						. " WHERE Usr_ID = ".quote_smart($Usr_ID)." LIMIT 1";
				$resUpd = $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//===============================================================================
				$sqlUpd	= "UPDATE sys_User_Client SET"
						. " Reg_No = ".quote_smart($regNo).",Company_No = ".quote_smart($companyNo).",Company_Name = ".quote_smart($companyName).","
						. " Address_Reg = ".quote_smart($address).",City_Reg = ".quote_smart($city).",State_Reg = ".quote_smart($state).",";
				if($postcode!='')
					$sqlUpd	.= " Postcode_Reg = ".quote_smart($postcode).",";
				$sqlUpd	.= " Address_Postal = ".quote_smart($p_address).",City_Postal = ".quote_smart($p_city).",State_Postal = ".quote_smart($p_state).",";
				if($p_postcode!='')
					$sqlUpd	.= " Postcode_Postal = ".quote_smart($p_postcode).",";
				$sqlUpd	.= " Phone_No = ".quote_smart($phoneNo).",Fax_No = ".quote_smart($faxNo).",Email = ".quote_smart($email).","
						. " Contact_Name = ".quote_smart($c_name).",Contact_Designation = ".quote_smart($c_designation).","
						. " Contact_Mobile_No = ".quote_smart($c_mobile).",Contact_Email = ".quote_smart($c_email).","
						. " Question_ID = ".quote_smart($security_Q).",Question_Answer = ".quote_smart($answer).""
						. " WHERE Usr_ID = ".quote_smart($Usr_ID)." LIMIT 1";
				$resUpd = $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				if($resUpd){ $msg = 'Update Successfully'; }
				else{ $msg = 'Update Unsuccessfully'; }
			}else{
				$sqlIns	= "INSERT INTO sys_User ("
						. " Usr_LoginID,Usr_Password,Level_ID,"
						. " RecordBy,RecordDate,UpdateBy,UpdateDate,"
						. " Active"
						. " ) VALUES ("
						. " ".quote_smart(strtoupper($userid)).",".quote_smart(md5('password')).",".quote_smart($levelID).","
						. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW(),"
						. " 1"
						. " )"
						;
				//echo $sqlIns;
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$Usr_ID	= $db->sql_nextid();
				//===============================================================================
				$sqlIns	= "INSERT INTO sys_User_Client ("
						. " Reg_No,Company_No,Company_Name,"
						. " Address_Reg,City_Reg,State_Reg,";
				if($postcode!='')
					$sqlIns	.= " Postcode_Reg,";
				$sqlIns	.= " Address_Postal,City_Postal,State_Postal,";
				if($p_postcode!='')
					$sqlIns	.= " Postcode_Postal,";
				$sqlIns	.= " Phone_No,Fax_No,Email,"
						. " Contact_Name,Contact_Designation,Contact_Mobile_No,Contact_Email,"
						. " Question_ID,Question_Answer,"
						. " Usr_ID"
						. " ) VALUES ("
						. " ".quote_smart($regNo).",".quote_smart($companyNo).",".quote_smart($companyName).","
						. " ".quote_smart($address).",".quote_smart($city).",".quote_smart($state).",";
				if($postcode!='')
					$sqlIns	.= " ".quote_smart($postcode).",";
				$sqlIns	.= " ".quote_smart($p_address).",".quote_smart($p_city).",".quote_smart($p_state).",";
				if($p_postcode!='')
					$sqlIns	.= " ".quote_smart($p_postcode).",";
				$sqlIns	.= " ".quote_smart($phoneNo).",".quote_smart($faxNo).",".quote_smart($email).","
						. " ".quote_smart($c_name).",".quote_smart($c_designation).",".quote_smart($c_mobile).",".quote_smart($c_email).","
						. " ".quote_smart($security_Q).",".quote_smart($answer).","
						. " ".quote_smart($Usr_ID).""
						. " )"
						;
				//echo $sqlIns;
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				if($resIns){ $msg = 'Insert Successfully'; }
				else{ $msg = 'Insert Unsuccessfully'; }
			}
			//===============================================================================
			func_add_audittrail($updBy,$msg.' ['.$Usr_ID.']','Admin-User','sys_User,'.$Usr_ID);//($recordBy='',$recordDesc='',$recordMod='',$recordOther='')
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				location.href="userProfile.php";
				exit();
				</script>
				';
		}
	}
	//===============================================================================
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
	//=====================
	$c_name			= '';
	$c_designation	= '';
	$c_mobile		= '';
	$c_email		= '';
	//=====================
	$userid		= '';
	//=====================
	$security_Q		= '';
	$answer			= '';
	$active			= 1;
	//===============================================================================
	if($Usr_ID!=0){
		$dataColumns = '*';
		$dataTable = 'sys_User u';
		$dataJoin = array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//===============================================================================
		foreach($arrayData as $detail){
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
			//=================================
			$c_name			= $detail['Contact_Name'];
			$c_designation	= $detail['Contact_Designation'];
			$c_mobile		= $detail['Contact_Mobile_No'];
			$c_email		= $detail['Contact_Email'];
			//=================================
			$userid		= $detail['Usr_LoginID'];
			//=================================
			$security_Q		= $detail['Question_ID'];
			$answer			= $detail['Question_Answer'];
			//=================================
		}
	}
	//===============================================================================
	$legendStyle = 'font-weight:bold;font-size:12px;';
	$requiredField = '<span style="color:red"> * </span>';
?>
	<script language="Javascript">
	function address_sameasabove(){
		var d = document;
		
		d.getElementById('p_address').value = d.getElementById('address').value;
		d.getElementById('p_city').value = d.getElementById('city').value;
		d.getElementById('p_postcode').value = d.getElementById('postcode').value;
		d.getElementById('p_state').value = d.getElementById('state').value;
	}
	function checkForm(){
		var d = document;
		/*
		var regNo = d.getElementById('regNo');
		if(regNo.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_REGISTRATION_NO ?>');
			regNo.focus();
			return false;
		}
		*/
		var companyNo = d.getElementById('companyNo');
		if(companyNo.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_COMPANY_NO ?>');
			companyNo.focus();
			return false;
		}
		var companyName = d.getElementById('companyName');
		if(companyName.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_COMPANY_NAME ?>');
			companyName.focus();
			return false;
		}
		var address = d.getElementById('address');
		if(address.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_REG_ADDRESS ?>');
			address.focus();
			return false;
		}
		var city = d.getElementById('city');
		if(city.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_CITY ?>');
			city.focus();
			return false;
		}
		var postcode = d.getElementById('postcode');
		if(postcode.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_POSTCODE ?>');
			postcode.focus();
			return false;
		}
		var state = d.getElementById('state');
		if(state.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_STATE ?>');
			state.focus();
			return false;
		}
		var phoneNo = d.getElementById('phoneNo');
		if(phoneNo.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_PHONE_NO ?>');
			phoneNo.focus();
			return false;
		}
		var email = d.getElementById('email');
		if(email.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_EMAIL ?>');
			email.focus();
			return false;
		}
		var c_name = d.getElementById('c_name');
		if(c_name.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_NAME ?>');
			c_name.focus();
			return false;
		}
		var c_designation = d.getElementById('c_designation');
		if(c_designation.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_DESIGNATION ?>');
			c_designation.focus();
			return false;
		}
		var c_mobile = d.getElementById('c_mobile');
		if(c_mobile.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_MOBILE_NO ?>');
			c_mobile.focus();
			return false;
		}
		var c_email = d.getElementById('c_email');
		if(c_email.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_EMAIL ?>');
			c_email.focus();
			return false;
		}
		var userid = d.getElementById('userid');
		if(userid.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_USERID ?>');
			userid.focus();
			return false;
		}
		return true;
	}
	$(document).ready(function() {
		$('#div_rel a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name:'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_USER_PROFILE .' :: '. $addEdit ); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<div id="div_rel">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="30">
			<td colspan="4" class="label title"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_LEVEL_TYPE ?></td>
			<td colspan="3"><label><strong><?php echo _LBL_CONTACT_TO_CHANGE . _LBL_LEVEL_TYPE ?></strong></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'regNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$regNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'companyNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$companyNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=compNo" name="<?= _LBL_HELP_COMP_NO_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_COMPANY_NAME . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'companyName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$companyName;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_REG_ADDRESS . $requiredField ?></td>
			<td width="30%" rowspan="3"><?php
				$param1 = 'address';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$address;
				$param3 = array('rows'=>'2');
				echo form_textarea($param1,$param2,$param3);
			?></td>
			<td class="label" width="20%"><?= _LBL_CITY . $requiredField ?></td>
			<td width="30%"><?php
				$param1 = 'city';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$city;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE . $requiredField ?></td>
			<td><?php
				$param1 = 'postcode';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$postcode;
				$param3 = array('type'=>'text','maxlength'=>'5','onKeyPress'=>'return numbersonly(this, event, false)');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=postcode" name="<?= _LBL_HELP_POSTCODE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE . $requiredField ?></td>
			<td><?php
				$param1 = 'state';
				$param2 = '';
				$param3 = _get_arraySelect('sys_State','State_ID','State_Name');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$state;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label" rowspan="3"><?= _LBL_POSTAL_ADDRESS ?><br /><a href="#" onClick="address_sameasabove();return false;"><?= _LBL_SAME_AS_ABOVE ?></a></td>
			<td width="30%" rowspan="3"><?php
				$param1 = 'p_address';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$p_address;
				$param3 = array('rows'=>'2');
				echo form_textarea($param1,$param2,$param3);
			?></td>
			<td class="label"><?= _LBL_CITY ?></td>
			<td><?php
				$param1 = 'p_city';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$p_city;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_POSTCODE ?></td>
			<td><?php
				$param1 = 'p_postcode';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$p_postcode;
				$param3 = array('type'=>'text','maxlength'=>'5','onKeyPress'=>'return numbersonly(this, event, false)');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=postcode" name="<?= _LBL_HELP_POSTCODE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><?php
				$param1 = 'p_state';
				$param2 = '';
				$param3 = _get_arraySelect('sys_State','State_ID','State_Name');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$p_state;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHONE_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'phoneNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$phoneNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=phone" name="<?= _LBL_HELP_PHONE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_FAX_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'faxNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$faxNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=fax" name="<?= _LBL_HELP_FAX_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'email';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$email;
				$param3 = array('type'=>'text','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."');",'style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=email" name="<?= _LBL_HELP_EMAIL_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents" height="30">
			<td colspan="4" class="label"><?= _LBL_CONTACT_PERSON ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_NAME . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'c_name';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$c_name;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESIGNATION . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'c_designation';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$c_designation;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_MOBILE_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'c_mobile';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$c_mobile;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=mobile" name="<?= _LBL_HELP_MOBILE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'c_email';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$c_email;
				$param3 = array('type'=>'text','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."');",'style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=email" name="<?= _LBL_HELP_EMAIL_TITLE ?>" >?</a></span> <?= _LBL_EMEL_NOTE2 ?></td>
		</tr>
		<tr class="contents" height="30">
			<td colspan="4" class="label title"><?= _LBL_USER_ID_PASSWORD ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_USERID . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'userid';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$userid;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents" height="30">
			<td colspan="4" class="label title"><?= _LBL_ANSWER_IF_FORGOT_ID_PASSWORD ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SECURITY_QUESTION ?></td>
			<td colspan="3"><?php
				$param1 = 'security_Q';
				$param2 = '';
				$param3 = _get_arraySelect('sys_Security_Question','Question_ID','Question_Desc');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$security_Q;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YOUR_ANSWER ?></td>
			<td colspan="3"><?php
				$param1 = 'answer';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$answer;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
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
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit','onClick'=>'return checkForm();');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'userProfile.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	</div>
	<br />
    </form>
	<?php func_window_close2(); ?>

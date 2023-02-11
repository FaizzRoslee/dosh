<?php 
	//****************************************************************/
	// filename: registration.php
	// description: form registration
	//****************************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."func_email.php";
	include_once $sys_config["includes_path"]."func_notification.php";
	include_once $sys_config["includes_path"]."func_date.php";
	//==========================================================
	func_header(_LBL_REGISTRATION,
				$sys_config["includes_path"]."css/global.css,". // css	
				$sys_config["includes_path"]."jquery/Qtip/qtip.css,". // css
				"",
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config["includes_path"]."jquery/Others/chromahash.jquery.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				true, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	if(isset($_POST["add"])){
		include $sys_config["includes_path"]."securimage/securimage.php";
		$img = new Securimage();
		$valid = $img->check($_POST["securityCode"]);
		//==========================================================
		if($valid == true) {
			$levelID 		= isset($_POST["levelID"])		? $_POST["levelID"]:0;
			$regNo	 		= isset($_POST["regNo"])		? $_POST["regNo"]:"";
			$companyNo 		= isset($_POST["companyNo"])	? $_POST["companyNo"]:"";
			$companyName 	= isset($_POST["companyName"])	? $_POST["companyName"]:"";
			$address		= isset($_POST["address"])		? $_POST["address"]:"";
			$city			= isset($_POST["city"])			? $_POST["city"]:"";
			$postcode		= isset($_POST["postcode"])		? $_POST["postcode"]:0;
			$state			= isset($_POST["state"])		? $_POST["state"]:0;
			$p_address		= isset($_POST["p_address"])	? $_POST["p_address"]:"";
			$p_city			= isset($_POST["p_city"])		? $_POST["p_city"]:"";
			$p_postcode		= isset($_POST["p_postcode"])	? $_POST["p_postcode"]:0;
			$p_state		= isset($_POST["p_state"])		? $_POST["p_state"]:0;
			$phoneNo		= isset($_POST["phoneNo"])		? $_POST["phoneNo"]:"";
			$faxNo			= isset($_POST["faxNo"])		? $_POST["faxNo"]:"";
			$email			= isset($_POST["email"])		? $_POST["email"]:"";
			//==========================================================
			$c_name			= isset($_POST["c_name"])		? $_POST["c_name"]:"";
			$c_designation	= isset($_POST["c_designation"])? $_POST["c_designation"]:"";
			$c_mobile		= isset($_POST["c_mobile"])		? $_POST["c_mobile"]:"";
			$c_email		= isset($_POST["c_email"])		? $_POST["c_email"]:"";
			//==========================================================
			$userid			= isset($_POST["userid"])		? $_POST["userid"]:"";
			$pswd			= isset($_POST["pswd"])			? $_POST["pswd"]:"";	
			$r_pswd			= isset($_POST["r_pswd"])		? $_POST["r_pswd"]:"";
			//==========================================================
			$security_Q		= isset($_POST["security_Q"])	? $_POST["security_Q"]:0;
			$answer			= isset($_POST["answer"])		? $_POST["answer"]:"";
			//==========================================================
			$checkLoginID	= _get_RowExist(
									"sys_User",
									array("AND Usr_LoginID"=>"= ".quote_smart($userid),"AND isDeleted"=>"= 0"),
									""
							);//$table="", $where=array(), $others=array()
			//==========================================================
			$checkCompNo	= _get_RowExist(
									"sys_User u",
									array("AND uc.Company_No"=>"= ".quote_smart($companyNo),"AND u.isDeleted"=>"= 0"),
									array("LEFT OUTER JOIN sys_User_Client uc ON u.Usr_ID = uc.Usr_ID")
							);//$table="", $where=array(), $others=array()
			//==========================================================
			if($checkLoginID>0 || $checkCompNo>0){
				$msg = "";
				if($checkLoginID>0) $msg .= "Login ID is not available";
				if($checkCompNo>0) $msg .= "Company No already registered with the system";
				//==========================================================
				echo '
					<script language="Javascript">
					alert("'.$msg.'");
					</script>
					';
			}
			else{
				$randomCode = createRandomString();
				//$currTimeDate = date('Y-m-d H:i:s');
				$sqlIns	= "INSERT INTO sys_User ("
						. " Usr_LoginID,Usr_Password,Level_ID,"
						. " RecordDate,UpdateDate,"
						. " Active"
						. " ) VALUES ("
						. " ".quote_smart(strtoupper($userid)).",".quote_smart(md5($pswd)).",".quote_smart($levelID).","
						. " NOW(),NOW(),"
						. " 0"
						. " )"
						;
				//echo $sqlIns;
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$usrID	= $db->sql_nextid();
				//==========================================================
				$sqlUpd	= "UPDATE sys_User SET RecordBy = ".quote_smart($usrID).", UpdateBy = ".quote_smart($usrID)." WHERE Usr_ID = ".quote_smart($usrID);
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				$sqlIns	= "INSERT INTO sys_User_Client ("
						. " Reg_No,Company_No,Company_Name,"
						. " Address_Reg,City_Reg,Postcode_Reg,State_Reg,"
						. " Address_Postal,City_Postal,";
				if(!empty($p_postcode)) $sqlIns .= " Postcode_Postal,";
				if(!empty($p_state)) $sqlIns .= " State_Postal,";
				$sqlIns	.= " Phone_No,Fax_No,Email,"
						. " Contact_Name,Contact_Designation,Contact_Mobile_No,Contact_Email,"
						. " Question_ID,Question_Answer,"
						. " Token,Usr_ID"
						. " ) VALUES ("
						. " ".quote_smart($regNo).",".quote_smart($companyNo).",".quote_smart($companyName).","
						. " ".quote_smart($address).",".quote_smart($city).",".quote_smart($postcode).",".quote_smart($state).","
						. " ".quote_smart($p_address).",".quote_smart($p_city).",";
				if(!empty($p_postcode)) $sqlIns	.= " ".quote_smart($p_postcode).",";
				if(!empty($p_state)) $sqlIns .= " ".quote_smart($p_state).",";
				$sqlIns	.= " ".quote_smart($phoneNo).",".quote_smart($faxNo).",".quote_smart($email).","
						. " ".quote_smart($c_name).",".quote_smart($c_designation).",".quote_smart($c_mobile).",".quote_smart($c_email).","
						. " ".quote_smart($security_Q).",".quote_smart($answer).","
						. " ".quote_smart($randomCode).",".quote_smart($usrID).""
						. " )"
						;
				//echo $sqlIns;
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				if($resIns){
					$userid = str_replace(" ","%20",$userid);
					$link = "http://".$_SERVER["SERVER_NAME"]. $sys_config["system_url"]."authentication/activate.php?loginid=".$userid."&token=".$randomCode;
					$content = _get_Notification(4,$usrID,0,$link); //4 is for registration
					if(is_array($content)){
						if($content["email"]!=""){
							func_email3($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
						}
					}
					//==========================================================
					echo '
						<script language="Javascript">
						alert("Your Registration not completed yet until you click activation link that sent to your mailbox!\nThe link will expired in 3 days.");
						location.href="'.$sys_config['system_url'].'";
						</script>
						';
				}else{
					echo '
						<script language="Javascript">
						alert("Your registration could not be sent");
						</script>
						';
				}
			}
		} 
		else {
			echo '
				<script language="Javascript">
				alert("Sorry, the code you entered was invalid.");
				//history.go(-1);
				</script>
				';
		}
	}
	//==========================================================
	$levelID 		= "";
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
	//========================
	$c_name			= "";
	$c_designation	= "";
	$c_mobile		= "";
	$c_email		= "";
	//========================
	$userid		= "";
	$pswd		= "";
	$r_pswd		= "";
	//========================
	$security_Q		= "";
	$answer			= "";
	$securityCode	= "";
	//========================
	$legendStyle = "font-weight:bold;font-size:12px;";
	$requiredField = '<span style="color:red"> * </span>';
	
	$spLoad = '<span class="sp-load"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
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
		
		var levelID = d.getElementById("levelID");
		if(levelID.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_REGISTRATION_TYPE ?>");
			levelID.focus();
			return false;
		}
		/*
		var regNo = d.getElementById("regNo");
		if(regNo.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_REGISTRATION_NO ?>");
			regNo.focus();
			return false;
		}
		*/
		var companyNo = d.getElementById("companyNo");
		if(companyNo.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_COMPANY_NO ?>");
			companyNo.focus();
			return false;
		}
		var companyName = d.getElementById("companyName");
		if(companyName.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_COMPANY_NAME ?>");
			companyName.focus();
			return false;
		}
		var address = d.getElementById("address");
		if(address.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_REG_ADDRESS ?>");
			address.focus();
			return false;
		}
		var city = d.getElementById("city");
		if(city.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_CITY ?>");
			city.focus();
			return false;
		}
		var postcode = d.getElementById("postcode");
		if(postcode.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_POSTCODE ?>");
			postcode.focus();
			return false;
		}
		var state = d.getElementById("state");
		if(state.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_STATE ?>");
			state.focus();
			return false;
		}
		var phoneNo = d.getElementById("phoneNo");
		if(phoneNo.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_PHONE_NO ?>");
			phoneNo.focus();
			return false;
		}
		var email = d.getElementById("email");
		if(email.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_EMAIL ?>");
			email.focus();
			return false;
		}
		var c_name = d.getElementById("c_name");
		if(c_name.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_NAME ?>");
			c_name.focus();
			return false;
		}
		var c_designation = d.getElementById("c_designation");
		if(c_designation.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_DESIGNATION ?>");
			c_designation.focus();
			return false;
		}
		var c_mobile = d.getElementById("c_mobile");
		if(c_mobile.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_MOBILE_NO ?>");
			c_mobile.focus();
			return false;
		}
		var c_email = d.getElementById("c_email");
		if(c_email.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_EMAIL ?>");
			c_email.focus();
			return false;
		}
		var userid = d.getElementById("userid");
		if(userid.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_USERID ?>");
			userid.focus();
			return false;
		}
		var pswd = d.getElementById("pswd");
		if(pswd.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_PASSWORD ?>");
			pswd.focus();
			return false;
		}
		var r_pswd = d.getElementById("r_pswd");
		if(r_pswd.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_RETYPE_PASSWORD ?>");
			r_pswd.focus();
			return false;
		}
		if(pswd.value!=r_pswd.value){
			alert("<?= _LBL_PASSWORD_DOESNOT_MATCH ?>");
			return false;
		}
		var security_Q = d.getElementById("security_Q");
		if(security_Q.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_SECURITY_QUESTION ?>");
			security_Q.focus();
			return false;
		}
		var securityCode = d.getElementById("securityCode");
		if(securityCode.value==""){
			alert("<?= _LBL_MSG_CANNOT_EMPTY . _LBL_SECURITY_CODE ?>");
			securityCode.focus();
			return false;
		}
		$("#add").button("disable").find(".sp-load").show();
		$("#div_rel").append("<input type=\"hidden\" name=\"add\" />");
		$("#myForm").submit();
		return true;
	}	
	function checkMatchPassword(val){
		var d = document;
		var password = d.getElementById("pswd").value;
		
		if(val!=password){
			d.getElementById("span_matchPswd").style.display = "";
		}else{
			d.getElementById("span_matchPswd").style.display = "none";
		}
		
	}
	$(document).ready(function() {
		$("#div_rel a[rel]").each(function() {
			$(this).qtip({
				content: { url: $(this).attr("rel"), title: { text: $(this).attr("name") } },
				position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },
				style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 200 } }
			});
		});
		$("input:password").chromaHash({bars: 3});
				
		$(".sp-load").hide();
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<center>
	<?php func_window_open2(_LBL_REGISTRATION,'70%'); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<div id="div_rel">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REGISTRATION_TYPE . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'levelID';
				$param2 = '';
				$param3 = _get_arraySelect('sys_Level','Level_ID','Level_Name',array('AND Level_Type'=>'= 2'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$levelID;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_REGISTRATION_NO ?></td>
			<td colspan="3"><?php
				$param1 = 'regNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$regNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_COMPANY_NO . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'companyNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$companyNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=compNo" name="<?= _LBL_HELP_COMP_NO_TITLE ?>" >?</a></span></td>
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
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=postcode" name="<?= _LBL_HELP_POSTCODE_TITLE ?>" >?</a></span></td>
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
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=postcode" name="<?= _LBL_HELP_POSTCODE_TITLE ?>" >?</a></span></td>
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
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=phone" name="<?= _LBL_HELP_PHONE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_FAX_NO ?></td>
			<td colspan="3"><?php
				$param1 = 'faxNo';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$faxNo;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=fax" name="<?= _LBL_HELP_FAX_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'email';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$email;
				$param3 = array('type'=>'text','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."');",'style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=email" name="<?= _LBL_HELP_EMAIL_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_CONTACT_PERSON ?></td>
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
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=mobile" name="<?= _LBL_HELP_MOBILE_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_EMAIL . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'c_email';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$c_email;
				$param3 = array('type'=>'text','onBlur'=>"return echeck(this.value,'". _LBL_NOTVALID_EMAIL ."');",'style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['authentication_path'] ?>help.php?help=email" name="<?= _LBL_HELP_EMAIL_TITLE ?>" >?</a></span>
			<?= "(". _LBL_USED_AS_COMMUNICATION .")"; ?></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_USER_ID_PASSWORD ?></td>
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
		<tr class="contents">
			<td class="label"><?= _LBL_PASSWORD . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'pswd';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$pswd;
				$param3 = array('type'=>'password');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_RETYPE_PASSWORD . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'r_pswd';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$r_pswd;
				$param3 = array('type'=>'password','onBlur'=>'checkMatchPassword(this.value);');
				echo form_input($param1,$param2,$param3);
			?>&nbsp;<span id="span_matchPswd" style="display:none;color:red;"><strong><?= _LBL_PASSWORD_DOESNOT_MATCH ?></strong></span></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_ANSWER_IF_FORGOT_ID_PASSWORD ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SECURITY_QUESTION . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'security_Q';
				$param2 = '';
				$param3 = _get_arraySelect('sys_Security_Question','Question_ID','Question_Desc');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$security_Q;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YOUR_ANSWER . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'answer';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$answer;
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_VERIFICATION_CODE ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SECURITY_CODE . $requiredField ?></td>
			<td colspan="3"><img id="siimage" align="left" style="padding-right: 5px; border: 0" src="<?= $sys_config['includes_path'] ?>securimage/securimage_show.php?sid=<?php echo md5(time()) ?>" /><a tabindex="-1" style="border-style: none" href="#" title="Refresh Image" onclick="document.getElementById('siimage').src = '<?= $sys_config['includes_path'] ?>securimage/securimage_show.php?sid=' + Math.random(); return false"><img src="<?= $sys_config['includes_path'] ?>securimage/images/refresh.gif" alt="Reload Image" border="0" onclick="this.blur()" align="bottom" /></a><div style="clear: both"></div><?php
				$param1 = 'securityCode';
				$param2 = '';
				$param3 = array('type'=>'text','style'=>'width:250px;');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				$dOthers = array("type"=>"button","onClick"=>"return checkForm();");
				echo form_button("add",_LBL_SUBMIT . $spLoad,$dOthers);
				echo "&nbsp;";
				$dOthers = array("type"=>"button","onClick"=>"location.href='".$sys_config['system_url']."'");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
			?></td>
		</tr>
	</table>
	<br />
    </div>
    </form>
	<?php func_window_close2(); ?>
	</center>
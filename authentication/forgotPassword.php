<?php 
	//****************************************************************/
	// filename: forgotPassword.php
	// description: forgot password
	//****************************************************************/
	include_once '../includes/sys_config.php';
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
	include_once $sys_config['includes_path'].'func_email.php';
	include_once $sys_config['includes_path'].'func_notification.php';
	//==========================================================
	func_header_new(_LBL_REGISTRATION,
				$sys_config['includes_path']."css/global.css,". // css	
				"", // css	
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
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
	if(isset($_POST['send'])){
		include $sys_config['includes_path'].'securimage/securimage.php';
		$img = new Securimage();
		$valid = $img->check($_POST['securityCode']);
		//==========================================================
		if($valid == true) {
			//================================
			$uID	= isset($_POST['uID'])?$_POST['uID']:'';
			$security_Q	= isset($_POST['security_Q'])?$_POST['security_Q']:'';
			if($security_Q=='') $security_Q = 0;
			$answer	= isset($_POST['answer'])?$_POST['answer']:'';
			//================================
			$table	= 'sys_User u';
			$where	= array('AND LOWER(Usr_LoginID)'=>'= '.quote_smart(strtolower($uID)),'AND Question_ID'=>'= '.quote_smart($security_Q),'AND Question_Answer'=>'= '.quote_smart($answer));
			$join	= array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
			$arrData = _get_arrayData('Email,u.Usr_ID',$table,$join,$where);
			if(count($arrData)>0){
				$email	= $arrData[0]['Email'];
				if($email!=''){
					$randomCode = createRandomString(8);
					$sqlUpd = "UPDATE sys_User SET Usr_Password = ".quote_smart(md5($randomCode))." WHERE Usr_ID = ".quote_smart($arrData[0]['Usr_ID'])." LIMIT 1";
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					$content = _get_Notification2(strtoupper($uID),$email,$randomCode); // ($loginID='',$email='',$newPswd='')
					if(is_array($content)){
						func_email3($sys_config['email_admin'],$content['email'],$content['title'],$content['body']);
					}
					echo '
						<script language="JavaScript">
						location.href="'.$sys_config['system_url'].'";
						</script>
						';
				}else{
					echo '
						<script language="JavaScript">
						alert("'. _LBL_NO_EMAIL .'");
						location.href="'.$sys_config['system_url'].'";
						</script>
						';
				}
			}else{
				echo '
					<script language="JavaScript">
					alert("'. _LBL_SEND_MAIL_FAILED .'");
					location.href="'.$sys_config['system_url'].'";
					</script>
					';
			}
		} else {
			echo '
				<script language="Javascript">
				alert("Sorry, the code you entered was invalid.");
				//history.go(-1);
				</script>
				';
		}
	}
	//==========================================================
	$uID	 		= '';
	$companyNo 		= '';
	//========================
	$security_Q		= '';
	$answer			= '';
	$securityCode	= '';
	//========================
	$legendStyle = 'font-weight:bold;font-size:12px;';
	$requiredField = '<span style="color:red"> * </span>';
?>
	<script language="Javascript">
	function checkForm(){
		var d = document;
		
		var uID = d.getElementById('uID');
		if(uID.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_USERID ?>');
			uID.focus();
			return false;
		}
		var security_Q = d.getElementById('security_Q');
		if(security_Q.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_SECURITY_QUESTION ?>');
			security_Q.focus();
			return false;
		}
		var answer = d.getElementById('answer');
		if(answer.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_YOUR_ANSWER ?>');
			answer.focus();
			return false;
		}
		var securityCode = d.getElementById('securityCode');
		if(securityCode.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_SECURITY_CODE ?>');
			securityCode.focus();
			return false;
		}
		return true;
	}	
	
	jQuery(document).ready(function(){
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<center>
	<?php func_window_open2(_LBL_FORGOT_PASSWORD,'70%'); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<div id="div_rel">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="28">
			<td colspan="4" class="label title" style="vertical-align:middle;"><?= _LBL_COMPANY_INFO ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_USERID . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'uID';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$uID;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
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
	<!--</fieldset>-->
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'send';
				$param2 = _LBL_SUBMIT;
				$param3 = array('type'=>'submit','onClick'=>'return checkForm();');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>"location.href='".$sys_config['system_url']."'");
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
    </div>
    </form>
	<?php func_window_close2(); ?>
	</center>
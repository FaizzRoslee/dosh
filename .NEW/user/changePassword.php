<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
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
				"", // css	
				$sys_config['includes_path']."jquery/Others/chromahash.jquery.js,". // javascript
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_md5.js,". // javascript
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
	if(isset($_POST['save'])){			
		//===============================================================================
		$pswd	= isset($_POST['pswd'])?$_POST['pswd']:'';	
		//===============================================================================
		$updBy = $Usr_ID;
		//===============================================================================
		if($Usr_ID!=0){
			$sqlUpd	= "UPDATE sys_User SET"
					. " Usr_Password = ".quote_smart(md5($pswd)).","
					. " UpdateBy = ".quote_smart($updBy).",UpdateDate = NOW()"
					//. " Active = ".quote_smart($active).""
					. " WHERE Usr_ID = ".quote_smart($Usr_ID)." LIMIT 1";
			$resUpd = $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			//===============================================================================
			if($resUpd){ $msg = 'Update Password Successfully'; }
			else{ $msg = 'Update Password Unsuccessfully'; }
			//===============================================================================	
		}
		func_add_audittrail($updBy,$msg.' ['.$Usr_ID.']','User-Profile','sys_User,'.$Usr_ID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="changePassword.php";
			</script>
			';
		exit();
	}
	if($Usr_ID!=0){
		$dataColumns = 'Usr_Password';
		$dataTable = 'sys_User';
		$dataJoin = '';
		$dataWhere = array("AND Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//=============================================================================
		foreach($arrayData as $detail){
			$c_pswd	= $detail['Usr_Password'];
		}
	}
	$legendStyle = 'font-weight:bold;font-size:12px;';
	$requiredField = '<span style="color:red"> * </span>';
?>
	<script language="Javascript">
	function checkForm(){
		var d = document;
		
		var c_pswd = d.getElementById('c_pswd');
		if(c_pswd.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_CURRENT_PASSWORD ?>');
			c_pswd.focus();
			return false;
		}
		var pswd = d.getElementById('pswd');
		if(pswd.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_NEW_PASSWORD ?>');
			pswd.focus();
			return false;
		}
		var r_pswd = d.getElementById('r_pswd');
		if(r_pswd.value==''){
			alert('<?= _LBL_MSG_CANNOT_EMPTY . _LBL_RETYPE_PASSWORD ?>');
			r_pswd.focus();
			return false;
		}
		if(pswd.value!=r_pswd.value){
			alert('<?= _LBL_PASSWORD_DOESNOT_MATCH ?>');
			r_pswd.focus();
			return false;
		}
	}
	function checkCurrentPassword(val){
		var d = document;
		var str_password;
		var currentPassword = "<?= $c_pswd ?>";
		str_password = hex_md5(val);
		
		if(currentPassword!=str_password){
			d.getElementById("span_cPswd").style.display = "";
			d.getElementById("save").style.display = "none";
		}else{
			d.getElementById("span_cPswd").style.display = "none";
			d.getElementById("save").style.display = "";
		}
		
	}
	function checkMatchPassword(val){
		var d = document;
		var password = d.getElementById('pswd').value;
		
		if(val!=password){
			d.getElementById('span_matchPswd').style.display = '';
		}else{
			d.getElementById('span_matchPswd').style.display = 'none';
		}
		
	}
	$(document).ready(function() {
		$('#div_rel a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		$("input:password").chromaHash({bars: 3});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_CHANGE_PASSWORD); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<div id="div_rel">
	<?= form_input('levelID',$levelID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CURRENT_PASSWORD . $requiredField ?></td>
			<td width="80%"><?php
				$param1 = 'c_pswd';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'password','onBlur'=>'checkCurrentPassword(this.value);');
				echo form_input($param1,$param2,$param3);
			?>&nbsp;<span id="span_cPswd" style="display:none;color:red;"><strong><?= _LBL_PASSWORD_DOESNOT_MATCH_CURRENT ?></strong></span></td>
		</tr>
		<span id="span_cromaHash">
		<tr class="contents">
			<td class="label"><?= _LBL_NEW_PASSWORD . $requiredField ?></td>
			<td><?php
				$param1 = 'pswd';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'password');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_RETYPE_PASSWORD . $requiredField ?></td>
			<td><?php
				$param1 = 'r_pswd';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'password','onBlur'=>'checkMatchPassword(this.value);');
				echo form_input($param1,$param2,$param3);
			?>&nbsp;<span id="span_matchPswd" style="display:none;color:red;"><strong><?= _LBL_PASSWORD_DOESNOT_MATCH_NEW ?></strong></span></td>
		</tr>
		</span>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				//if($levelID>5){ $formBack =  'userProfile.php'; }
				//else {  
					$formBack =  'changePassword.php'; 
				//}
				$param1 = 'save';
				$param2 = _LBL_SUBMIT;
				$param3 = array('type'=>'submit','onClick'=>'return checkForm();','style'=>'display:none');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\''.$formBack.'\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	</div>
	<br />
    </form>
	<?php func_window_close2(); ?>

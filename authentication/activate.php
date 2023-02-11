<?php 
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
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
	include_once $sys_config['includes_path'].'func_date.php';
	//==========================================================
	func_header(_LBL_REGISTRATION,
				$sys_config['includes_path']."css/global.css", // css	
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."media/js/jquery_tip.js,". // javascript
				$sys_config['includes_path']."media/js/jtip.js", // javascript
				false, // menu
				false, // portlet
				true, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	if(isset($_GET["loginid"]) && isset($_GET["token"])){
		$userid = str_replace("%20"," ",$_GET["loginid"]);
		$token = $_GET["token"];
		//====================================================
		$dateRec = _get_StrFromCondition("sys_User_Client","(SELECT DATE_FORMAT(RecordDate,'%Y-%m-%d') FROM sys_User WHERE Usr_ID = sys_User_Client.Usr_ID)","Token",$token);

		$expired = false;
		if(!empty($dateRec)){
			$expiredDate = date("Y-m-d", strtotime("$dateRec +3 day"));
			if(date("Y-m-d") > $expiredDate){
				$expired = true;
				$notification = 'Your Activation Link has Expired. Please contact system administration. Click <a href="'.$sys_config['system_url'].'">here</a> to return.';
			}
		}
		
		if($expired == false){
			//====================================================
			$sqlIns	= "INSERT INTO sys_user_reg ("
					. " Usr_ID,Reg_Date"
					. " ) VALUES ("
					. " ".quote_smart(_get_StrFromCondition('sys_User','Usr_ID','Usr_LoginID',$userid)).",NOW()"
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$regID	= $db->sql_nextid();
			//====================================================
			$new_regID = 'DOSH/'.date('Y').'/'. substr('000000000'.$regID,-6) .'/R';
			//====================================================
			$sql	= "UPDATE sys_User u"
					. " LEFT OUTER JOIN sys_User_Client uc ON u.Usr_ID = uc.Usr_ID"
					. " SET"
					. " uc.Reg_ID = ".quote_smart($new_regID).","
					. " u.ActivationDate = NOW(),"
					. " u.UpdateDate = NOW(),"
					. " u.Active = 1,"
					. " uc.Token = NULL"
					. " WHERE LOWER(u.Usr_LoginID) = ".quote_smart(strtolower($userid))." AND uc.Token = ".quote_smart($_GET['token'])."";
			//echo $sql;
			$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
			if($db->sql_affectedrows() > 0){
				$notification = 'Activation Succes. Click <a href="'.$sys_config['system_url'].'">here</a> to login.';
				//notification=============================================
				$usrid = _get_StrFromManyConditions('sys_User','Usr_ID',array('AND LOWER(Usr_LoginID)'=>'= '.quote_smart(strtolower($userid))));
				$content = _get_Notification(1,$usrid); //1 is for registration
				if(is_array($content)){
					if($content['email']!=''){
						func_email2($sys_config['email_admin'],$content['email'],$content['title'],$content['body']);
					}
				}
				//notification=============================================
			}else{
				$notification = 'No Activation has been done. Click <a href="'.$sys_config['system_url'].'authentication/registration.php">here</a> to register.';
			}
		}
	}else{
		$notification = 'No Activation has been done. Click <a href="'.$sys_config['system_url'].'authentication/registration.php">here</a> to register.';
	}
?>
	<center>
	<?php func_window_open2('','70%'); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="0" cellpadding="3" width="100%" align="center">
		<tr class="contents" height="30">
			<td align="center"><?= $notification ?></td>
		</tr>
    </form>
	<br />
	<?php func_window_close2(); ?>
	</center>
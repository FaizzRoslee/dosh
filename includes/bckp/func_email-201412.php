<?php
	/*********************************************************************************/
	//	File name		:		func_email.php
	//	Date created	:		6 Dec 2010
	/**********************************************************************************/

	require_once "email.config.php";
	require_once "phpmailer/class.phpmailer.php";
	
	function sendmail($email_content=array()){
		global $mail_config;
	
		$mail = new PHPMailer(false); 	// the true param means it will throw exceptions on errors, which we need to catch
		
		if($mail_config["smtp_mode"]=="enabled")
			$mail->IsSMTP(); 			// telling the class to use SMTP
			
		try {
			if($mail_config["smtp_mode"]=="enabled")
				$mail->Host		= $mail_config["smtp_host"]; 	// SMTP server

			$mail->SMTPDebug	= $mail_config["debug"];
			
			if(!empty($mail_config["smtp_auth"]))
				$mail->SMTPAuth		= $mail_config["smtp_auth"];	// enable SMTP authentication
		
			if(!empty($mail_config["smtp_secure"]))
				$mail->SMTPSecure 	= $mail_config["smtp_secure"];	// sets the prefix to the servier
				
			if(!empty($mail_config["smtp_port"]))
				$mail->Port			= $mail_config["smtp_port"];
				
			if(!empty($mail_config["smtp_username"]))
				$mail->Username   	= $mail_config["smtp_username"];	
				
			if(!empty($mail_config["smtp_password"]))
				$mail->Password   	= $mail_config["smtp_password"];	
			
			if(isset($email_content["emailFrom"]) && !empty($email_content["emailFrom"])){
				$mail->SetFrom($email_content["emailFrom"]);
			}
			else $mail->SetFrom($mail_config["from_email"], $mail_config["from_name"]);
			
			if(!empty($email_content["emailTo"])){
				foreach($email_content["emailTo"] as $email){
					if(isset($email[0]) && !empty($email[0])){
						$mail->AddAddress($email[0], $email[1]);
					}
				}
			}
			
			$mail->ConfirmReadingTo = $mail_config["from_email"];
						
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional - MsgHTML will create an alternate automatically
			
			$mail->Subject = $email_content["subject"];
			$mail->MsgHTML( $email_content["body"] );
			
			// if(!empty($attach)){
				// foreach($attach as $file){
					// if(file_exists($file[0])){
						// if(isset($file[1]) && !empty($file[1])) $mail->AddAttachment($file[0],$file[1]);
						// else $mail->AddAttachment($file[0]);
					// }
				// }
			// }

			$msg = "";
			if(!$mail->Send()){
				$msg = _LBL_SEND_MAIL_FAILED;
			}
			else{
				$msg = "success";
			}
			
			return $msg;
		}
		catch (phpmailerException $e) {
			return $e->errorMessage(); //Pretty error messages from PHPMailer
		} 
		catch (Exception $e) {
			return $e->getMessage(); //Boring error messages from anything else!
		}
	}
	
	function func_email($strFrom="",$strTo="",$strSubject="",$strMsg=""){
		
		//--Signature--//		   
		$str_signature = "<br><p>Daripada,<br>"
					   . " - ". _LBL_CIMS ." - <br>"
					   . "<b><font size=\"2\" face=\"Verdana\" color=\"red\">"
					   . "<span style=\"font-size: 10pt; font-family: Verdana; color: red; font-weight: bold;\">"
					   . _LBL_DOSH ." </span></font></b></p>";
					   
		$strMsg  = $strMsg.$str_signature;
		
		$arr = array(
				"emailTo" => array(array($strTo,"")),
				"subject" => $strSubject,
				"body" => $strMsg
			);
			
		
		if ($strTo!="" && $strFrom!=""){
			$send = sendmail($arr);
			if($send=="success"){
					echo "<script language='JavaScript'>";
					echo "alert('". _LBL_SEND_MAIL_SUCCESS ." ".$strTo."');";
					echo "</script>";
			}
			else{
				echo "<script language='JavaScript'>";
				echo "alert('". _LBL_SEND_MAIL_FAILED ."');";
				echo "</script>";
			}
		}
		else{
			echo "<script language='JavaScript'>";
			echo "alert('". _LBL_NO_EMAIL ."');";
			echo "</script>";
		}	
	}
	
	function func_email2($strFrom="",$strTo="",$strSubject="",$strMsg=""){
		$arr = array(
				"emailTo" => array(array($strTo,"")),
				"subject" => $strSubject,
				"body" => $strMsg
			);
			
		if($strTo!="" && $strFrom!=""){	
			$send = sendmail($arr);
			/*
			if($send=="success"){
					echo "<script language='JavaScript'>";
					echo "alert('". _LBL_SEND_MAIL_SUCCESS ." ".$strTo."');";
					echo "</script>";
			}
			else{
				echo "<script language='JavaScript'>";
				echo "alert('". _LBL_SEND_MAIL_FAILED ."');";
				echo "</script>";
			}
			*/
		}
	}
	
	function func_email3($strFrom="",$strTo="",$strSubject="",$strMsg=""){	
		$arr = array(
				"emailTo" => array(array($strTo,"")),
				"subject" => $strSubject,
				"body" => $strMsg
			);

		if (!empty($strTo) && !empty($strFrom)){
			$send = sendmail($arr);
			if($send=="success"){
					echo "<script language='JavaScript'>";
					echo "alert('". _LBL_SEND_MAIL_SUCCESS ." ".$strTo."');";
					echo "</script>";
			}
			else{
				echo "<script language='JavaScript'>";
				echo "alert('". _LBL_SEND_MAIL_FAILED ."');";
				echo "</script>";
			}
		}
		else {
			echo "<script language='JavaScript'>";
			echo "alert('". _LBL_NO_EMAIL ."');";
			echo "</script>";
		}
	}
	
	function func_email4($strFrom="",$strTo="",$strSubject="",$strMsg=""){
		$arr = array(
				"emailFrom" => $strFrom,
				"emailTo" => array(array($strTo,"")),
				"subject" => $strSubject,
				"body" => $strMsg
			);
		
		$send = sendmail($arr);
		
		if($send=="success") return _LBL_SEND_MAIL_SUCCESS;
		else return _LBL_SEND_MAIL_FAILED;
		
	}
?>

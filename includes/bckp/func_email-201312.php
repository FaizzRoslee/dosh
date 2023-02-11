<?php
	/*********************************************************************************/
	//	File name		:		func_email.php
	//	Date created	:		6 Dec 2010
	/**********************************************************************************/
	
	function func_email($strFrom='',$strTo='',$strSubject='',$strMsg=''){	
		global $sys_config;
		
		//--Header--//
		$str_mail_header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
		$str_mail_header .= "From: ".$strFrom;	
		
		//--Signature--//		   
		$str_signature = "<br><p>Daripada,<br>"
					   . " - ". _LBL_CIMS ." - <br>"
					   . "<b><font size=\"2\" face=\"Verdana\" color=\"red\">"
					   . "<span style=\"font-size: 10pt; font-family: Verdana; color: red; font-weight: bold;\">"
					   . _LBL_DOSH ." </span></font></b></p>";
					   
		//--Mesej + Signature--//		   
		$strMsg  = $strMsg.$str_signature;
		
		if ($strTo!='' && $strFrom!=''){	
			$send = mail($strTo, $strSubject, $strMsg, $str_mail_header );
			if($send){	
					echo "<script language='JavaScript'>";
					echo "alert('". _LBL_SEND_MAIL_SUCCESS ." ".$strTo."');";
					echo "</script>";
			}else{
				echo "<script language='JavaScript'>";
				echo "alert('". _LBL_SEND_MAIL_FAILED ."');";
				echo "</script>";
			}
		}
		else
		{
			echo "<script language='JavaScript'>";
			echo "alert('". _LBL_NO_EMAIL ."');";
			echo "</script>";
		}	
	}
	
	function func_email2($strFrom='',$strTo='',$strSubject='',$strMsg=''){
		global $sys_config;
		
		//--Header--//
		$header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
		$header .= "From: ".$strFrom;
		
		if($strTo!='' && $strFrom!=''){	
			$send = mail($strTo,$strSubject,$strMsg,$header);
		}
	}
	
	function func_email3($strFrom='',$strTo='',$strSubject='',$strMsg=''){	
		global $sys_config;
		
		//--Header--//
		$header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
		$header .= "From: ".$strFrom;

		if ($strTo!='' && $strFrom!=''){
			$send = mail($strTo, $strSubject, $strMsg, $header );
			if($send){
					echo "<script language='JavaScript'>";
					echo "alert('". _LBL_SEND_MAIL_SUCCESS ." ".$strTo."');";
					echo "</script>";
			}else{
				echo "<script language='JavaScript'>";
				echo "alert('". _LBL_SEND_MAIL_FAILED ."');";
				echo "</script>";
			}
		}
		else
		{
			echo "<script language='JavaScript'>";
			echo "alert('". _LBL_NO_EMAIL ."');";
			echo "</script>";
		}
	}
	
	function func_email4($strFrom='',$strTo='',$strSubject='',$strMsg=''){
		global $sys_config;
		
		//--Header--//
		$header = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
		$header .= "From: ". $strFrom  . "\r\n";
		$header .= "Reply-To: ". $strFrom  . "\r\n";
		$header .= "Cc: ". $strFrom  . "\r\n";
		
		$send = mail($strTo,$strSubject,$strMsg,$header);
		
		if($send) return _LBL_SEND_MAIL_SUCCESS;
		else return _LBL_SEND_MAIL_FAILED;
		
	}
?>

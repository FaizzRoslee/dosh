<?php
	//****************************************************************/
	// filename: func_notification.php
	// description: notification
	//****************************************************************/

	/*****************************
	 * notification
	 *****************************/
	function _get_Notification($cat=0,$usr_id=0,$sid=0,$link=''){
		global $db;
		//==================================
		$arr_emel['email'] = '';
		$arr_emel['title'] = '';
		$arr_emel['body'] = '';
		//==================================
		if($cat!=0 && $usr_id!=0){
			$dataColumns 	= 'uc.Company_Name,uc.Company_No,u.Level_ID,u.ActivationDate,u.RecordDate,uc.Reg_ID,uc.Contact_Email,u.Usr_LoginID';
			$dataTable 		= 'sys_User u';
			$dataJoin 		= array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
			$dataWhere 		= array('AND u.Usr_ID'=>'= '.quote_smart($usr_id),'AND u.isDeleted'=>'= 0');
			$arrData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
			//print_r($arrData);
			//==================================
			foreach($arrData as $data){
				$compName = $data['Company_Name'];
				$compNo = $data['Company_No'];
				$compLvl = _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$data['Level_ID']);
				$actDate = func_ymd2dmy($data['ActivationDate']);
				$regDate = func_ymd2dmy($data['RecordDate']);
				$regID = $data['Reg_ID'];
				$email	= $data['Contact_Email'];
				$userLoginID = $data['Usr_LoginID'];
			}
			$arr_emel['email'] = $email;
			//==================================
			//$compInfo = '<pre>';
			$compInfo = '	<strong>Company Info</strong>';
			$compInfo .= '	<br />';
			$compInfo .= '	Company Name: '.$compName;
			$compInfo .= '	<br />';
			$compInfo .= '	Company Registration No: '.$compNo;
			$compInfo .= '	<br />';
			$compInfo .= '	Type of Supplier: '.$compLvl;
			$compInfo .= '	<br />';
			$compInfo .= '	<br />';
			//$compInfo .= '</pre>';
			//==================================
			//$bodyLower = '<pre>';
			$bodyLower = '	Yours sincerely,';
			$bodyLower .= '	<br />';
			$bodyLower .= '	JKKP System';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Tel : +603-8871 1229';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Fax : +603-8889 2339';
			$bodyLower .= '	<br />';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Please note: this is an auto generated e-mail that cannot receive replies.';
			//$bodyLower .= '</pre>';
			//==================================
			if($cat==1){ //registration acknowledge
				//==================================
				$title = 'JKKP System, Account Registration Successful';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your account was successful activated and registered with our system';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Registration Info</strong>';
				$str .= '	<br />';
				$str .= '	Registration ID: '.$regID;
				$str .= '	<br />';
				$str .= '	Registration Date: '.$regDate;
				$str .= '	<br />';
				$str .= '	Activation Date: '.$actDate;
				$str .= '	<br />';
				$str .= '	Registered User ID: '.$userLoginID;
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Please login to the system to retrieve the submission certificate.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//$str .= '</pre>';
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==2){ //acknowledge new
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 11','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Chemical Submission Acknowledgement';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical submission was acknowledged by JKKP.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Acknowledgement Date: '. func_ymd2dmy(_get_StrFromCondition('tbl_Submission','ApprovedDate','Submission_ID',$sid));
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//$str .= '</pre>';
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			//==================================
			elseif($cat==3){ //acknowledge renew
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 12','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Chemical Submission Renewal Acknowledgement';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical submission renewal was acknowledged by JKKP.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Renewal Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Acknowledgement Date: '. func_ymd2dmy(_get_StrFromCondition('tbl_Submission','ApprovedDate','Submission_ID',$sid));
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//$str .= '</pre>';
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==4){ //acknowledge renew
				//==================================
				$title = 'JKKP System, Account Activation';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Thank you for registering at JKKP System.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	<strong>Your Details</strong>';
				$str .= '	<br />';
				$str .= '	Company Name: '.$compName;
				$str .= '	<br />';
				$str .= '	Company Registration No: '.$compNo;
				$str .= '	<br />';
				$str .= '	Type: '.$compLvl;
				$str .= '	<br />';
				$str .= '	Email: '.$email;
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Please click link below to activate and login to your account. This link will expired in 3 days.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '<a href="'. $link .'" target="_blank">'. $link .'</a>';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==5){ //new submission sent for approval
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 11','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Submission';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical was sent to DOSH for acknowledgement.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==6){ //renew submission sent for approval
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 12','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Submission Renewal';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical was sent to DOSH for acknowledgement.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Renewal Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==7){ //new submission rejected
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 11','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Submission Not Acknowledged';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical Submission was not acknowledged by DOSH.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==8){ //renew submission rejected
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 12','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Submission Renewal Not Acknowledged';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	Your chemical Submission Renewal was not acknowledged by DOSH.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Renewal Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==9){ //reminder
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 12','ORDER BY'=>'Sub_Record_ID DESC');
				//==================================
				$title = 'JKKP System, Reminder to Submission Renewal';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	You submitted chemical inventory need to be renewed.';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Submission Info</strong>';
				$str .= '	<br />';
				$str .= '	Submission ID: '. _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);
				$str .= '	<br />';
				$str .= '	Submission Date: '. _getSubmitDate($sid);
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
			elseif($cat==20){ //forgot password
				//==================================
				$title = 'JKKP System, Forgot Password';
				//==================================
				//$str = '<pre>';
				$str = '	Dear Sir,';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= '	You have requested Login Info. Please change your password after login using the given password';
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $compInfo;
				$str .= '	<strong>Login Info</strong>';
				$str .= '	<br />';
				$str .= '	Login ID: '. $loginID;
				$str .= '	<br />';
				$str .= '	New Password: '. func_ymd2dmy(_get_StrFromManyConditions('tbl_Submission_Record','Sub_Record_Date',$where));
				$str .= '	<br />';
				$str .= '	<br />';
				$str .= $bodyLower;
				//==================================
				$arr_emel['title'] = $title;
				$arr_emel['body'] = $str;
				//==================================
			}
		}
		return $arr_emel;
	}
	/*****************************/
	
	/*****************************
	 * notification of retrieving password
	 *****************************/
	function _get_Notification2($loginID='',$email='',$newPswd=''){
		global $db;
		//==================================
		$arr_emel['email'] = '';
		$arr_emel['title'] = '';
		$arr_emel['body'] = '';
		//==================================
		if($loginID!='' && $email!=''){
			$bodyLower = '	Yours sincerely,';
			$bodyLower .= '	<br />';
			$bodyLower .= '	JKKP System';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Tel : +603-8871 1229';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Fax : +603-8889 2339';
			$bodyLower .= '	<br />';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Please note: this is an auto generated e-mail that cannot receive replies.';
			
			$arr_emel['email'] = $email;
			$arr_emel['title'] = 'JKKP System, Forgot Password';
			//==================================
			$str = '	Dear Sir,';
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= '	You have requested Login Info. Please change your password after login using the given password';
			$str .= '	<br />';
			$str .= '	<br />';
			//$str .= $compInfo;
			$str .= '	<strong>Login Info</strong>';
			$str .= '	<br />';
			$str .= '	Login ID: '. $loginID;
			$str .= '	<br />';
			$str .= '	New Password: '. $newPswd;
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= $bodyLower;
			//==================================
			$arr_emel['body'] = $str;
			//==================================
		}
		return $arr_emel;
	}
	/*****************************/
	
	/*****************************
	 * notification of submission update 
	 *****************************/
	function _get_Notification3($email='',$sid=0){ //submission update
		global $db;
		//==================================
		$arr_emel['email'] = $email;
		$arr_emel['title'] = '';
		$arr_emel['body'] = '';
		//==================================
		if($sid!=0){
			$usr_id = _get_StrFromCondition('tbl_Submission','Usr_ID','Submission_ID',$sid);
			//==================================
			$dataColumns 	= 'uc.Company_Name,uc.Company_No,u.Level_ID,u.ActivationDate,u.RecordDate,uc.Reg_ID,uc.Contact_Email';
			$dataTable 		= 'sys_User u';
			$dataJoin 		= array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
			$dataWhere 		= array('AND u.Usr_ID'=>'= '.quote_smart($usr_id),'AND u.isDeleted'=>'= 0');
			$arrData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
			//==================================
			foreach($arrData as $data){
				$compName = $data['Company_Name'];
				$compNo = $data['Company_No'];
				$compLvl = _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$data['Level_ID']);
			}
			//==================================
			$compInfo = '	<strong>Company Info</strong>';
			$compInfo .= '	<br />';
			$compInfo .= '	Company Name: '.$compName;
			$compInfo .= '	<br />';
			$compInfo .= '	Company Registration No: '.$compNo;
			$compInfo .= '	<br />';
			$compInfo .= '	Type of Supplier: '.$compLvl;
			$compInfo .= '	<br />';
			$compInfo .= '	<br />';
			//==================================
			$bodyLower = '	Yours sincerely,';
			$bodyLower .= '	<br />';
			$bodyLower .= '	JKKP System';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Tel : +603-8871 1229';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Fax : +603-8889 2339';
			$bodyLower .= '	<br />';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Please note: this is an auto generated e-mail that cannot receive replies.';
			//==================================
			$submissionID = _get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$sid);	
			// if(count(explode('/',$submissionID)) == 3){ // new submission
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 11','ORDER BY'=>'Sub_Record_ID DESC');
			// }else{
				// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'= 12','ORDER BY'=>'Sub_Record_ID DESC');
			// }
			// $where = array('AND Submission_ID'=>'= '.quote_smart($sid),'AND Sub_Record_Status'=>'IN (11,12)','ORDER BY'=>'Sub_Record_ID DESC');
			//==================================
			$title = 'JKKP System, Submission Updated';
			//==================================
			//$str = '<pre>';
			$str = '	Dear Sir,';
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= '	Submission Inventory Updated.';
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= $compInfo;
			$str .= '	<strong>Submission Info</strong>';
			$str .= '	<br />';
			$str .= '	Submission ID: '. $submissionID;
			$str .= '	<br />';
			$str .= '	Submission Date: '. _getSubmitDate($sid);
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= $bodyLower;
			//==================================
			$arr_emel['title'] = $title;
			$arr_emel['body'] = $str;
			//==================================
		}
		return $arr_emel;
	}
	/*****************************/
	
	/*****************************
	 * notification of reset password by admin
	 *****************************/
	function _get_Notification4($loginID="",$email=""){
		global $db;
		//==================================
		$arr_emel["email"] = "";
		$arr_emel["title"] = "";
		$arr_emel["body"] = "";
		//==================================
		if($loginID!="" && $email!=""){
			$bodyLower = '	Yours sincerely,';
			$bodyLower .= '	<br />';
			$bodyLower .= '	JKKP System';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Tel : +603-8871 1229';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Fax : +603-8889 2339';
			$bodyLower .= '	<br />';
			$bodyLower .= '	<br />';
			$bodyLower .= '	Please note: this is an auto generated e-mail that cannot receive replies.';
			
			$arr_emel['email'] = $email;
			$arr_emel['title'] = 'JKKP System, Reset Password';
			//==================================
			$str = '	Dear Sir,';
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= '	You have requested Administrator to reset your password. Please change your password after login using the given password';
			$str .= '	<br />';
			$str .= '	<br />';
			//$str .= $compInfo;
			$str .= '	<strong>Login Info</strong>';
			$str .= '	<br />';
			$str .= '	Login ID: '. $loginID;
			$str .= '	<br />';
			$str .= '	New Password: '. $loginID;
			$str .= '	<br />';
			$str .= '	<br />';
			$str .= $bodyLower;
			//==================================
			$arr_emel['body'] = $str;
			//==================================
		}
		return $arr_emel;
	}
	/*****************************/

?>
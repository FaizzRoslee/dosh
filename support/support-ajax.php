<?php	
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
	//***************************************************************/
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
	// include_once $sys_config['includes_path'].'func_notification.php';
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'func_email.php';
	// include_once $sys_config['includes_path'].'arrayCommon.php';

	if(isset($_POST['fn'])){
		$arr = array();
		if($_POST['fn']=='contact'){
			$msg = '';
			$sSQL = '';
			
			$id 		= isset($_POST['id']) ? $_POST['id'] : '';
			$Usr_ID 	= isset($_POST['Usr_ID']) ? $_POST['Usr_ID'] : '';
			$contact 	= isset($_POST['contact-info']) ? $_POST['contact-info'] : '';
			
			if(isset($_POST['ftype']) && $_POST['ftype']=='edit'){
				// $msg = "Okay";
				$sql = "UPDATE sys_Info SET "
					. " Info_Desc = ". quote_smart($contact) .","
					. " UpdateBy = ". quote_smart($Usr_ID) .","
					. " UpdateDate = NOW()"
					. " WHERE Info_ID = ". quote_smart($id);
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				$sSQL .= $sql .'<br />';
				
				if($res) $msg = "Update Successfully!!";
				else $msg = "Update Failed!!";
			}
			elseif(isset($_POST['ftype']) && $_POST['ftype']=='search'){
				$msg = "Search";
			}
			
			$arr = array(
					'result' => $msg,
					// 'sSQL' => $sSQL
				);
		}
		elseif($_POST['fn']=='feedback'){
			$msg = '';
			$sSQL = '';
		
			$fName 		= isset($_POST['fName']) ? $_POST['fName'] : '';
			$fContact 	= isset($_POST['fContact']) ? $_POST['fContact'] : '';
			$fEmail 	= isset($_POST['fEmail']) ? $_POST['fEmail'] : '';
			$fCategory 	= isset($_POST['fCategory']) ? $_POST['fCategory'] : '';
			$fSubject 	= isset($_POST['fSubject']) ? $_POST['fSubject'] : '';
			$fMessage 	= isset($_POST['fMessage']) ? $_POST['fMessage'] : '';
			$Usr_ID 	= isset($_POST['Usr_ID']) ? $_POST['Usr_ID'] : '';
			$fCategoryL	= isset($_POST['fCategory-label']) ? $_POST['fCategory-label'] : '';
			
			if(isset($_POST['ftype']) && $_POST['ftype']=='add'){
				$sql = "INSERT INTO tbl_Feedback ("
					. " Feedback_Name, Feedback_Contact, Feedback_Email,"
					. " Type_ID,"
					. " Feedback_Subject, Feedback_Message,"
					. " RecordBy, RecordDate"
					. " ) VALUES ("
					. quote_smart($fName) .", ". quote_smart($fContact) .", ". quote_smart($fEmail) .", "
					. quote_smart($fCategory) .", "
					. quote_smart($fSubject) .", ". quote_smart($fMessage) .", "
					. quote_smart($Usr_ID) .", NOW()"
					. ")";
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				$sSQL .= $sql .'<br />';
				
				if($res){
					$msg = _LBL_THANK_FEEDBACK;
					
					$mailFrom = $fEmail;
					if( !empty($sys_config['email_admin']) || !empty($mailFrom) ){
						$fMessage .= '
								<br /><br /><br />
								From,<br />
								'. $fName .'<br />
								'. $fContact .'
								';
						$mailSubj = 'Feedback - CIMS '. $fCategoryL .': '. $fSubject;
						$mailMsgs = nl2br($fMessage);
						$mailSend = func_email4($mailFrom,$sys_config['email_admin'],$mailSubj,$mailMsgs);
					}
				}
				else $msg = _LBL_FAILED_FEEDBACK;
			}
			
			$arr = array(
					'result' => $msg,
					'mail' => $mailSend,
					// 'sSQL' => $sSQL
				);
		}
		elseif($_POST['fn']=='faq'){
			$msg = '';
			$sSQL = '';
			
			$faq_q_eng 	= isset($_POST['faq_q_eng']) ? $_POST['faq_q_eng'] : '';
			$faq_a_eng 	= isset($_POST['faq_a_eng']) ? $_POST['faq_a_eng'] : '';
			$faq_q_may 	= isset($_POST['faq_q_may']) ? $_POST['faq_q_may'] : '';
			$faq_a_may 	= isset($_POST['faq_a_may']) ? $_POST['faq_a_may'] : '';
			$Usr_ID 	= isset($_POST['Usr_ID']) ? $_POST['Usr_ID'] : '';
			$id 		= isset($_POST['id']) ? $_POST['id'] : '';
			
			if(isset($_POST['ftype']) && $_POST['ftype']=='add'){
				$sql = "INSERT INTO tbl_Faq ("
					. " Faq_Q_eng, Faq_A_eng,"
					. " Faq_Q_may, Faq_A_may,"
					. " RecordBy, RecordDate, UpdateBy, UpdateDate"
					. " ) VALUES ("
					. quote_smart($faq_q_eng) .", ". quote_smart($faq_a_eng) .", "
					. quote_smart($faq_q_may) .", ". quote_smart($faq_a_may) .", "
					. quote_smart($Usr_ID) .", NOW(), ". quote_smart($Usr_ID) .", NOW()"
					. ")";
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				$sSQL .= $sql .'<br />';

				if($res) $msg = "Insert Successfully";
				else $msg = "Insert Unsuccessfully";
			}
			elseif(isset($_POST['ftype']) && $_POST['ftype']=='edit'){
				$sql = "UPDATE tbl_Faq SET "
					. " Faq_Q_eng = ". quote_smart($faq_q_eng) .", "
					. " Faq_A_eng = ". quote_smart($faq_a_eng) .", "
					. " Faq_Q_may = ". quote_smart($faq_q_may) .", "
					. " Faq_A_may = ". quote_smart($faq_a_may) .", "
					. " UpdateBy = ". quote_smart($Usr_ID) .", "
					. " UpdateDate = NOW()"
					. " WHERE Faq_ID = ". quote_smart($id)
					;
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				$sSQL .= $sql .'<br />';
				
				if($res) $msg = "Update Successfully";
				else $msg = "Update Unsuccessfully";
			}
			elseif(isset($_POST['ftype']) && $_POST['ftype']=='delete'){
				$chk 	= isset($_POST['checkbox']) ? $_POST['checkbox'] : array();
				
				if(count($chk)>0){
					$im_chk = implode(',',$chk);
					$sql = "UPDATE tbl_Faq SET"
						. " isDeleted = 1,"
						. " UpdateBy = ". quote_smart($Usr_ID) .", "
						. " UpdateDate = NOW()"
						. " WHERE Faq_ID IN (". quote_smart($im_chk,false) .")"
						;
					$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
					$sSQL .= $sql .'<br />';
					
					if($res) $msg = "Delete Successfully";
					else $msg = "Delete Unsuccessfully";					
				}
			}
			
			$arr = array(
					'result' => $msg
					// ,'sSQL' => $sSQL
				);
		}
		elseif($_POST["fn"]=="reply"){
			if(isset($_POST["ftype"]) && $_POST["ftype"]=="add"){

				$id 		= isset($_POST["id"]) 		? $_POST["id"] 		: "";
				$Usr_ID 	= isset($_POST["Usr_ID"]) 	? $_POST["Usr_ID"] 	: "";
				$reply 		= isset($_POST["reply"]) 	? $_POST["reply"] 	: "";
				$fName 		= isset($_POST["fName"]) 	? $_POST["fName"] 	: "";
				$fType 		= isset($_POST["fType"]) 	? $_POST["fType"] 	: "";
				$fEmail 	= isset($_POST["fEmail"]) 	? $_POST["fEmail"] 	: "";
				$fSubject 	= isset($_POST["fSubject"]) ? $_POST["fSubject"] : "";
				$fMessage 	= isset($_POST["fMessage"]) ? $_POST["fMessage"] : "";
				$fTime 		= isset($_POST["fTime"]) 	? $_POST["fTime"] 	: "";
				
				$fromName = $fromEmail = "";
				
				$dataTable = "sys_User_Staff";
				$dataColumns = "User_Fullname,User_Email";
				$dataJoin = "";
				$dataWhere = array("AND Usr_ID" => "= ". quote_smart($Usr_ID));
				$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
				foreach($arrayData as $detail){
					$fromName	= $detail["User_Fullname"];
					$fromEmail 	= $detail["User_Email"];					
				}
				
				$sql = "UPDATE tbl_Feedback SET Status = 2, ReplyMessage = ". quote_smart($reply) .", ReplyBy = ". quote_smart($Usr_ID) .", ReplyDate = NOW() WHERE Feedback_ID = ". quote_smart($id) ." LIMIT 1";
				// echo $sql;die;
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				
				$fBody = "";
				$mailFrom = $sys_config["email_admin"];
				if( !empty($mailFrom) && !empty($fEmail) ){
					$fBody .= "Time: ". func_ymd2dmytime($fTime) ."<br />"
							. "Feedback:<br />"
							. nl2br($fMessage)
							. "<br /><br />"
							. "Reply:<br />"
							. nl2br($reply)
							;
					$fBody .= "<br /><br /><br />"
							. "From,<br />"
							. $fromName
							;
					$mailSubj = 'RE: Feedback - '. $fType .': '. $fSubject;
					$mailMsgs = $fBody;
					$mailSend = func_email4($mailFrom,$fEmail,$mailSubj,$mailMsgs,array(array($fromEmail,$fromName)));

					$msg = $mailSend ." ". $fEmail;
				}
			}
			
			$arr = array(
					"result" => $msg
				);
		}
		elseif($_POST["fn"]=="deletefeedback"){
			$id = isset($_POST["id"]) ? $_POST["id"] : "";
			
			$sql = "DELETE FROM tbl_Feedback WHERE Feedback_ID IN ($id)";
			$res = $db->sql_query($sql,END_TRANSACTION);
			
			if($res) $msg = _LBL_RECORD_DELETE_SUCCESS;
			else $msg = _LBL_RECORD_DELETE_FAILED;
			
			$arr = array("result"=>$msg);
		}
		
		echo json_encode($arr);
	}
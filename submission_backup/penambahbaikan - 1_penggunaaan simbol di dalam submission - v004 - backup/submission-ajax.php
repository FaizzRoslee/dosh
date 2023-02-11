<?php	
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
	//***************************************************************/
 
/*****************************************************************

    Update 20211101 : LinuxHouse is currently reponsible for the maintenance of this CIMS
                      system ( 11 years old ). 

                      Our contract ends approx 202310.

                      With regards to this  file, modifications made by us are for;

                      - adding footnotes


******************************************************************/
	include_once "../includes/sys_config.php";
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*********/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_notification.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."func_email.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."formElement.php";

	$msg = "";
	if(isset($_POST["fn"])){
		$arr = array();
		if($_POST["fn"]=="bulk-submission"){
			$msg = "";
			$Usr_ID = isset($_POST["Usr_ID"]) ? $_POST["Usr_ID"] : "";
			
			$sSQL = "";
			
			if(isset($_POST["ftype"]) && $_POST["ftype"]=="sub"){
				$confirm 	= isset($_POST["ch_confirm1"]) ? $_POST["ch_confirm1"] : array();
				$disclaimer	= isset($_POST["disclaimer1"]) ? 1 : 0;
				$status 	= isset($_POST["disclaimer1"]) ? $_POST["disclaimer1"] : 1;
				
				if(count($confirm)>0){
					$remark 	= isset($_POST["data-remark"]) ? $_POST["data-remark"] : "";
					// print_r($_POST);die;
					$sqlS = "INSERT INTO tbl_Submission ("
						. " Usr_ID, Type_ID, Submission_Remark, Status_ID,"
						. " BulkSubmission, Disclaimer, RecordBy, RecordDate"
						. " ) VALUES ("
						. quote_smart($Usr_ID) .", 1, ". quote_smart($remark) .", ". quote_smart($status) .", "
						. quote_smart("B") .", ". quote_smart($disclaimer) .", ". quote_smart($Usr_ID) .", NOW()"
						. ")";
					$sSQL .= $sqlS ."<br />";
					$resS = $db->sql_query($sqlS,END_TRANSACTION) or die(print_r($db->sql_error()));
					$sID = $db->sql_nextid();
					
					if($resS){ $msg = "Submission Save Successfully"; }
					else{ $msg = "Submission Save Unsuccessfully"; }
					func_add_audittrail($Usr_ID,$msg." [".$sID."]","Submission-New","tbl_Submission,".$sID);
						
					foreach($confirm as $row){

						$form	 	= isset($_POST["data-form"][$row]) 		? $_POST["data-form"][$row] 	: "";
						$chemical 	= isset($_POST["data-chemical"][$row]) 	? $_POST["data-chemical"][$row] : "";
						$casno	 	= isset($_POST["data-casno"][$row]) 	? $_POST["data-casno"][$row] 	: "";
						$pHazard	= isset($_POST["data-pHazard"][$row]) 	? $_POST["data-pHazard"][$row] 	: "";
						$hHazard	= isset($_POST["data-hHazard"][$row]) 	? $_POST["data-hHazard"][$row] 	: "";
						$eHazard	= isset($_POST["data-eHazard"][$row]) 	? $_POST["data-eHazard"][$row] 	: "";
						$import		= isset($_POST["data-import"][$row]) 	? number_format($_POST["data-import"][$row],1) 	: "";
						$manufact	= isset($_POST["data-manufact"][$row]) 	? number_format($_POST["data-manufact"][$row],1) : "";
						
						

						// linuxhouse_20220610
						// start penambahbaikan - 6_masalah new chemical - v005: with wrongly spelled chemical name in excel file 1
						// NOTE: 
						// if 'importer/supplier' registers chemical in 'Upload Bulk Submission' isNew=1. 
                        // - it appears in 'Inventory/New Chemical' at admin site
                        // - counted in 'Administrator/home/New Chemical'
                        // if 'administrator' registers chemical in 'INVENTORY' isNew=0.
                        // - it appears in 'Administrator/Inventory/Chemical List' 
                        // - not counted in 'Administrator/Home/New Chemical'
                        if($casno!=''){	// if 'CAS No' is not empty, check if it already exist in the database				
                            $dataColumns = "Chemical_ID, Chemical_Name, Chemical_CAS";
                            $dataTable = "tbl_chemical";
                            $dataJoin = "";
                            $dataWhere = array("AND Chemical_CAS" => "= ".quote_smart($casno), 'AND isActive' => '= 1', 'AND isNew' => '= 0', 'AND isDeleted' => '= 0');
                            // gets chemical id, name and cas no
                            $arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
                            // check if array result is empty
                            if(count($arrayData) > 0)
                            {
                                foreach($arrayData as $detail){
                                    // $exist = 1;
                                    $chemical_id_db = $detail['Chemical_ID'];
                                    $chemical_name_db = $detail['Chemical_Name'];
                                    $chemical_cas_db = $detail['Chemical_CAS'];
                                    
                                    // if chemical name from database is equal to 'Product/Chemical Name' from interface
                                    //  - use 'Product/Chemical Name' from interface
                                    // else 
                                    //  - use chemical name from database
                                    if(strcmp(quote_smart($chemical_name_db), quote_smart($chemical)) !== 0)
                                        $chemical = $chemical_name_db;
                                }
                            }
                        }
						// end penambahbaikan - 6_masalah new chemical - v005: with wrongly spelled chemical name in excel file 1
						
						
						
						$sqlD = "INSERT INTO tbl_Submission_Detail ("
							. " Form_ID, Category_ID_ph, Category_ID_hh, Category_ID_eh,"
							. " Detail_TotalSupply, Detail_TotalUse, Submission_ID, RecordDate"
							. " ) VALUES ("
							. quote_smart(getForm($form)) .", ". quote_smart(getHazard($pHazard,1)) .","
							. quote_smart(getHazard($hHazard,2)) .", ". quote_smart(getHazard($eHazard,3)) .", "
							. quote_smart(str_replace(",","",$import)) .", ". quote_smart(str_replace(",","",$manufact)) .", ". quote_smart($sID) .", NOW()"
							. ")";
						$sSQL .= "<br />". $sqlD ."<br />";
						$resD = $db->sql_query($sqlD,END_TRANSACTION) or die(print_r($db->sql_error()));
						$dID = $db->sql_nextid();
						
						$sqlC = "INSERT INTO tbl_Submission_Chemical ("
							. " Chemical_ID, Detail_ID, RecordDate"
							. " ) VALUES ("
							. quote_smart( getChemical($chemical,$casno,$Usr_ID) ) .", ". quote_smart($dID) .", NOW()"
							. ")";
						$sSQL .= $sqlC ."<br />";
						$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
					}
				}
			}
			elseif(isset($_POST["ftype"]) && $_POST["ftype"]=="mix"){
				$confirm = isset($_POST["ch_confirm2"]) ? $_POST["ch_confirm2"] : array();
				$disclaimer	= isset($_POST["disclaimer2"]) ? 1 : 0;
				$status 	= isset($_POST["disclaimer2"]) ? $_POST["disclaimer2"] : 1;
				// print_r($confirm);
				if(count($confirm)>0){
					$remark 	= isset($_POST["data-remark"]) ? $_POST["data-remark"] : "";
					// print_r($_POST);die;
					$sqlS = "INSERT INTO tbl_Submission ("
						. " Usr_ID, Type_ID, Submission_Remark, Status_ID,"
						. " BulkSubmission, Disclaimer, RecordBy, RecordDate"
						. " ) VALUES ("
						. quote_smart($Usr_ID) .", 2, ". quote_smart($remark) .", ". quote_smart($status) .", "
						. quote_smart('B') .", ". quote_smart($disclaimer) .", ". quote_smart($Usr_ID) .", NOW()"
						. ")";
					$sSQL .= $sqlS ."<br />";
					$resS = $db->sql_query($sqlS,END_TRANSACTION) or die(print_r($db->sql_error()));
					$sID = $db->sql_nextid();
					
					if($resS){ $msg = "Submission Save Successfully"; }
					else{ $msg = "Submission Save Unsuccessfully"; }
					func_add_audittrail($Usr_ID,$msg." [".$sID."]","Submission-New","tbl_Submission,".$sID);
					
					foreach($confirm as $row){
						
						// $remark 	= isset($_POST["data-remark"][$row]) ? $_POST["data-remark"][$row] : "";
						$product 	= isset($_POST["data-product"][$row]) 	? $_POST["data-product"][$row] 	: "";
						$form	 	= isset($_POST["data-form"][$row]) 		? $_POST["data-form"][$row] 	: "";
						$idnumber 	= isset($_POST["data-idnumber"][$row]) 	? $_POST["data-idnumber"][$row] : "";
						$ingredient = isset($_POST["data-ingredient"][$row])? $_POST["data-ingredient"][$row] : "";
						$pHazard	= isset($_POST["data-pHazard"][$row]) 	? $_POST["data-pHazard"][$row] 	: "";
						$hHazard	= isset($_POST["data-hHazard"][$row]) 	? $_POST["data-hHazard"][$row] 	: "";
						$eHazard	= isset($_POST["data-eHazard"][$row]) 	? $_POST["data-eHazard"][$row] 	: "";
						$import		= isset($_POST["data-import"][$row]) 	? number_format((float)$_POST["data-import"][$row],1) 	: "";
						$manufact	= isset($_POST["data-manufact"][$row]) 	? number_format((float)$_POST["data-manufact"][$row],1) : "";
						
						$sqlD = "INSERT INTO tbl_Submission_Detail ("
							. " Detail_ProductName, Detail_ID_Number,"
							. " Form_ID, Category_ID_ph, Category_ID_hh, Category_ID_eh,"
							. " Detail_TotalSupply, Detail_TotalUse, Submission_ID, RecordDate"
							. " ) VALUES ("
							. quote_smart($product) .", ". quote_smart($idnumber) .", "
							. quote_smart(getForm($form)) .", ". quote_smart(getHazard($pHazard,1)) .", "
							. quote_smart(getHazard($hHazard,2)) .", ". quote_smart(getHazard($eHazard,3)) .", "
							. quote_smart(str_replace(",","",$import)) .", ". quote_smart(str_replace(",","",$manufact)) .", ". quote_smart($sID) .", NOW()"
							. ")";
						$sSQL .= $sqlD .'<br />';
						$resD = $db->sql_query($sqlD,END_TRANSACTION) or die(print_r($db->sql_error()));
						$dID = $db->sql_nextid();
						
						for($i=1; $i<=$ingredient; $i++){
							$chemical	= isset($_POST["data-chemical"][$row][$i]) 		? $_POST["data-chemical"][$row][$i] 	: "";
							$casno		= isset($_POST["data-casno"][$row][$i]) 		? $_POST["data-casno"][$row][$i] 		: "";
							$composition = isset($_POST["data-composition"][$row][$i]) 	? $_POST["data-composition"][$row][$i] 	: "";
							$source 	= isset($_POST["data-source"][$row][$i]) 		? $_POST["data-source"][$row][$i] 		: "";
							
							
							
							
                            // linuxhouse_20220614
                            // start penambahbaikan - 8_ruangan chemical source - v004: automatically set 'Chemical Source' code
                            
                            // automatically set source code to the code of 'No Information'
                            // get source array from arrayCommon.php
                            $master_array = $arrSource;
                            // gets third element of array
                            $master_array = $master_array[3];
                            // gets value(s) of array
                            $ni_code = $master_array['code'];
                            // assign the code to source or 'Chemical Source'
                            $source = ($ni_code) ? $ni_code : "";
                            
                            // end penambahbaikan - 8_ruangan chemical source - v004: automatically set 'Chemical Source' code
							
							
							
							
						// linuxhouse_20220610
						// start penambahbaikan - 6_masalah new chemical - v005: with wrongly spelled chemical name in excel file 2
						// NOTE: 
						// if 'importer/supplier' registers chemical in 'Upload Bulk Submission' isNew=1. 
                        // - it appears in 'Inventory/New Chemical' at admin site
                        // - counted in 'Administrator/home/New Chemical'
                        // if 'administrator' registers chemical in 'INVENTORY' isNew=0.
                        // - it appears in 'Administrator/Inventory/Chemical List' 
                        // - not counted in 'Administrator/Home/New Chemical'
                        if($casno!=''){	// if 'CAS No' is not empty, check if it already exist in the database				
                            $dataColumns = "Chemical_ID, Chemical_Name, Chemical_CAS";
                            $dataTable = "tbl_chemical";
                            $dataJoin = "";
                            $dataWhere = array("AND Chemical_CAS" => "= ".quote_smart($casno), 'AND isActive' => '= 1', 'AND isNew' => '= 0', 'AND isDeleted' => '= 0');
                            // gets chemical id, name and cas no
                            $arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
                            // check if array result is empty
                            if(count($arrayData) > 0)
                            {
                                foreach($arrayData as $detail){
                                    // $exist = 1;
                                    $chemical_id_db = $detail['Chemical_ID'];
                                    $chemical_name_db = $detail['Chemical_Name'];
                                    $chemical_cas_db = $detail['Chemical_CAS'];
                                    
                                    // if chemical name from database is equal to 'Product/Chemical Name' from interface
                                    //  - use 'Product/Chemical Name' from interface
                                    // else 
                                    //  - use chemical name from database
                                    if(strcmp(quote_smart($chemical_name_db), quote_smart($chemical)) !== 0)
                                        $chemical = $chemical_name_db;
                                }
                            }
                        }
						// end penambahbaikan - 6_masalah new chemical - v005: with wrongly spelled chemical name in excel file 2
							
							
						
							$sqlC = "INSERT INTO tbl_Submission_Chemical ("
								. " Chemical_ID,"
								. " Detail_ID, Sub_Chemical_Composition, Sub_Chemical_Source, RecordDate"
								. " ) VALUES ("
								. quote_smart( getChemical($chemical,$casno,$Usr_ID) ) .", "
								. quote_smart($dID) .", ". quote_smart( getComposition($composition) ) .", ". quote_smart( getSource($source) ) .", NOW()"
								. ")";
							$sSQL .= $sqlC ."<br />";
							$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
						}
					}
				}
			}
				
			if( isset($sID) && !empty($sID) && $status==11 ){
				$sqlI = "INSERT INTO tbl_Submission_Submit (Submission_ID,SubmitBy,SubmitTime)"
					. " VALUES (". quote_smart($sID) .",". quote_smart($Usr_ID) .",NOW())";
				$resI = $db->sql_query($sqlI,END_TRANSACTION) or die(print_r($db->sql_error()));
				$submitID = $db->sql_nextid();
				
				if($submitID!=""){
					/*****/
					$submitID = _generateSubmissionID($submitID);
					// $submitID = "DOSH/". date("Y") ."/". substr("000000000". $submitID,-7); 
					
					$sqlUpd = "UPDATE tbl_Submission SET";
					$sqlUpd .= " Submission_Submit_ID = ". quote_smart($submitID) .",";
					$sqlUpd .= " UpdateBy = ". quote_smart($Usr_ID) .","
							. " UpdateDate = NOW()"
							. " WHERE Submission_ID = " .quote_smart($sID) ." LIMIT 1";
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					//==========================================================
					if($resUpd){ 
						$msg = "Submission was sent for checking"; 
						$sqlIns = "INSERT INTO tbl_Submission_Record (Sub_Record_Desc,Sub_Record_Remark,Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID)"
								. " VALUE (".quote_smart($msg).",".quote_smart($remark).",".quote_smart($Usr_ID).",NOW(),".quote_smart($status).",".quote_smart($sID).")";
						$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
						//==========================================================
						func_add_audittrail($Usr_ID,$msg." [".$sID."]","Submission-New","tbl_Submission,".$sID);
						$msg .= "\nSubmission ID: [".$submitID."]"; 
						//==========================================================
						$cat = 5;
				
						$content = _get_Notification($cat,$Usr_ID,$sID); //
						if(is_array($content)){
							if($content["email"]!="")
								func_email2($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
							else
								$msg .= _LBL_NO_EMAIL;
						}
					}else{ 
						$msg = "Submission cannot sent for checking"; 
						func_add_audittrail($Usr_ID,$msg." [".$sID."]","Submission-New","tbl_Submission,".$sID);
					}
					//==========================================================
				}
			}
			
			if(file_exists($_POST["filepath"])){
				if(unlink($_POST["filepath"]))
					$unlink = "Success";
				else
					$unlink = "Failed";
			}
			else{
				$unlink = "No such file";
			}
			
			$arr = array(
					"message" => $msg,
					"unlink" => $unlink,
					"sSQL" => $sSQL
				);
			echo json_encode($arr);
		}
		elseif($_POST["fn"]=="unlink"){
			$path = isset($_POST["fpath"]) ? $_POST["fpath"] : "";
			if(file_exists($path)){
				unlink($path);
				$msg = "Success";
			}
			else $msg = "Failed";
			
			$arr = array(
					"message" => $msg
				);
			echo json_encode($arr);
		}
		elseif($_POST["fn"]=="submission"){
			$chemType 		= isset($_POST["chemType"]) 	? $_POST["chemType"] 					: "";
			$quantity 		= isset($_POST["quantity"]) 	? sanitizeOne($_POST["quantity"],"plain") : 0;
			$levelID 		= isset($_POST["levelID"]) 		? $_POST["levelID"] 					: "";
			$submissionID 	= isset($_POST["submissionID"]) ? $_POST["submissionID"] 				: "";
			
			$str = "";
			
			$str_onClick = "";
			$requiredField = '<span style="color:red"> * </span>';

   // linuxhouse_20211107
   // footnotes init 
   // start

   $footNotePhysicalHazardClassification    = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_PHY_HAZARD_CLASS_FOOTNOTE.'</span>'; 
   $footNoteHealthHazardClassification      = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_HEALTH_HAZARD_CLASS_FOOTNOTE.'</span>';  
   $footNoteEnvironmentHazardClassification = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_ENV_HAZARD_CLASS_FOOTNOTE.'</span>'; 
   $footNoteQuantityImported                = '<br><span style=" color:blue; font-weight:normal; ">'._LBL_QUANTITY_IMPORTED_FOOTNOTE.'</span>'; 
   $footNoteQuantityManufactured            = '<br><span style=" color:blue; font-weight:normal; ">'._LBL_QUANTITY_MANUFACTURED_FOOTNOTE.'</span>';   

   // end 
			
			if($quantity>0){
				$statusID 	= _get_StrFromCondition("tbl_Submission","Status_ID","Submission_ID",$submissionID);
				if(empty($statusID)) $statusID = 1;
				
				$dColumns 	= "Detail_ID,Detail_ProductName,Detail_ID_Number,Form_ID,Category_ID_ph,Category_ID_hh,Category_ID_eh,"
							. "Detail_TotalSupply,Detail_TotalUse";
				$dTable 	= "tbl_Submission_Detail";
				$dJoin 		= "";
				$dWhere 	= array("AND Submission_ID" => "= ".quote_smart($submissionID),"AND isDeleted"=>"= 0");
				$arrData 	= _get_arrayData($dColumns,$dTable,$dJoin,$dWhere); 
			
				// print_r($arrData);
				for($i=1;$i<=$quantity;$i++){
				
					$classified = 0;
					$productName 	= "";
					$idNo 			= "";
					$old			= "";
					$detailID 		= "";
					$chemicalID 	= "";
					$subChemID 		= "";
					$newchemicalID 	= "";
					$physicalForm 	= "";
					$chemicalName 	= "";
					$casNo 			= "";
					$noIngredient 	= "";
					
					$selectTo_PH 	= array();
					$selectTo_HH 	= array();
					$selectTo_EH 	= array();
					
					$totalSupply 	= "";
					$totalUse 		= "";
					
					if(isset($arrData[$i-1])){
						$productName 	= $arrData[$i-1]["Detail_ProductName"];
						$idNo			= $arrData[$i-1]["Detail_ID_Number"];
						$old			= $arrData[$i-1]["old"];
						$detailID 		= $arrData[$i-1]["Detail_ID"];
						$physicalForm 	= $arrData[$i-1]["Form_ID"];
						
						$selectTo_PH 	= explode(",",$arrData[$i-1]["Category_ID_ph"]);
						$selectTo_HH 	= explode(",",$arrData[$i-1]["Category_ID_hh"]);
						$selectTo_EH 	= explode(",",$arrData[$i-1]["Category_ID_eh"]);
				
						$totalSupply 	= $arrData[$i-1]["Detail_TotalSupply"];
						$totalUse 		= $arrData[$i-1]["Detail_TotalUse"];
					}
					
					// if( empty($totalSupply) ) $totalSupply 	= "0.0";
					// if( empty($totalUse) ) $totalUse = "0.0";
					
					if($chemType==1){
					
						$dColumns2 	= "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate,old";
						$dTable2 	= "tbl_Submission_Chemical";
						$dJoin2 	= "";
						$dWhere2 	= array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
						$arrData2	= _get_arrayData($dColumns2,$dTable2,$dJoin2,$dWhere2); 
						foreach($arrData2 as $detail){
							$subChemID	 	= $detail["Sub_Chemical_ID"];
							$chemicalID 	= $detail["Chemical_ID"];
							$chemicalName 	= !empty($detail["Sub_Chemical_Name"])?$detail["Sub_Chemical_Name"]
																				:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
							$composition 	= !empty($detail["Sub_Chemical_Composition"])?$detail["Sub_Chemical_Composition"]:"-";
							$source		 	= $detail["Sub_Chemical_Source"];
							$casNo			= !empty($detail["Sub_Chemical_CAS"])?$detail["Sub_Chemical_CAS"]
																				:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
							$nolonger		= !empty($detail["StopDate"])?func_ymd2dmy($detail["StopDate"]):"-";
							
							$isCWC			= _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$chemicalID);
						
							if(!empty($chemicalID)){
								$dTable = "tbl_Chemical_Classified";
								$dWhere = array("AND Chemical_ID"=>"= ".quote_smart($chemicalID),"AND Active"=>"= 1");
								$rowExist = _get_RowExist($dTable,$dWhere);
								if($rowExist>0){
									$classified = 1;
									$catIDs = _get_StrFromManyConditions($dTable,"Category_ID",$dWhere);
									if($catIDs!="") $arr_catIDs = explode(",",$catIDs);
								}
							}
						}
				
					$str .= '<br />'
						. '<table border="0" class="contents cls-click-detect" cellspacing="1" cellpadding="3" width="100%" id="tbl'. $i .'">'
						. 	'<tr class="contents">'
						.		'<td class="label title" colspan="3"><input type="checkbox" name="chkSelect_'.$i.'" id="chkSelect_'.$i.'" value="1" checked class="cl_chkbx" />'
						. 			_LBL_SUBSTANCE .' '. $i .'</td>'
                                                .               '<td width="1%" bgcolor="#3333cc"  align="right" ><button class="btn btn-danger btn-xs cls-delete-cem" value="'.$detailID.'" title="'. _LBL_REMOVE_SUBSTANCE .'"><span class="glyphicon glyphicon-remove"></span></button></td> '

						.	'</tr>'
						.	'<tr class="contents">'
						.		'<td width="20%" class="label">'. _LBL_PHYSICAL_FORM . $requiredField .'</td>'
						.		'<td colspan="3">'
						;
					
						$dName 		= "physicalForm$i";
						$dOptions 	= _get_arraySelect("tbl_Chemical_Form","Form_ID","Form_Name",array("AND Active"=>"= 1"),"Form_ID");
						$str .= form_select($dName,"",$dOptions,$physicalForm,"") 
							. '<div class="error"><label for="'. $dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
					$str .= 	'</td>'
						.	'</tr>'
						.	'<tr class="contents">'
						.		'<td width="20%" class="label">'. _LBL_TRADE_PRODUCT . $requiredField .'</td>'
						.		'<td width="30%">'
						;
					
						$str .= form_input("detailID_$i",$detailID,array("type"=>"hidden"));
						$str .= form_input("old_$i",$old,array("type"=>"hidden"));
						$str .= form_input("classified_$i",$classified,array("type"=>"hidden"));
						$str .= form_input("noIngredient$i",1,array("type"=>"hidden"));
						$str .= form_input("chemicalID_".$i."_1",$chemicalID,array("type"=>"hidden"));
						$str .= form_input("subChemID_".$i."_1",$subChemID,array("type"=>"hidden"));
						
						$dName 		= "chemicalName_". $i ."_1";
						$dOthers 	= array("type"=>"text","readonly"=>"readonly","style"=>"width:90%;","class"=>"getSubChemical");
						$str .= form_input($dName,$chemicalName,$dOthers)
							. '<div class="error"><label for="'. $dName .'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
					$str .= 	'</td>'
						.		'<td width="20%" class="label">'. _LBL_CAS_NO .'</td>'
						.		'<td width="30%">'
						;
					
						$dName 		= "casNo_".$i."_1";
						$dOthers 	= array("type"=>"text","readonly"=>"readonly","class"=>"getSubChemical");
						$str .= '<span class="ignoreValidate">'. form_input($dName,$casNo,$dOthers) .'</span>'; 
						
					$str .= 	'</td>'
						.	'</tr>'
						;
					
					}
					else{
					
						$dColumns2 	= "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
						$dTable2 	= "tbl_Submission_Chemical";
						$dJoin2 	= "";
						$dWhere2 	= array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
						$arrData2	= _get_arrayData($dColumns2,$dTable2,$dJoin2,$dWhere2); 
				
						if(!empty($submissionID)) $noIngredient = count($arrData2);
					
					$str .= '<br />'
						. '<table border="0" class="contents cls-click-detect" cellspacing="1" cellpadding="3" width="100%" id="tbl'. $i .'">'
						.	'<tr class="contents">'
						.		'<td  class="label title" colspan="3"><input type="checkbox" name="chkSelect_'.$i.'" id="chkSelect_'.$i.'" value="1" checked class="cl_chkbx" />'
						. 		_LBL_MIXTURE .' '. $i .'</td>'
                                                .               '<td width="1%" bgcolor="#3333cc"  align="right" ><button class="btn btn-danger btn-xs cls-delete-mix" value="'.$detailID.'" title="'. _LBL_REMOVE_MIXTURE .'"><span class="glyphicon glyphicon-remove"></span></button></td> '
						.	'</tr>'
						.	'<tr class="contents">'
						.		'<td class="label">'. _LBL_PRODUCT_NAME . $requiredField .'</td>'
						.		'<td colspan="3">'
						;
					
						$str .= form_input("detailID_$i",$detailID,array("type"=>"hidden"));
						$str .= form_input("classified_$i","",array("type"=>"hidden"));
						
						$dName 		= "productName$i";
						$dOthers 	= array("type"=>"text","style"=>"width:400px;");
						$str .= form_input($dName,$productName,$dOthers)
							. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
					$str .= 	'</td>'
						.	'</tr>'
						.	'<tr class="contents">'
						.		'<td width="20%" class="label">'. _LBL_PHYSICAL_FORM . $requiredField .'</td>'
						.		'<td width="30%">'
						;
					
						$dName 		= "physicalForm$i";
						$dOptions 	= _get_arraySelect('tbl_Chemical_Form','Form_ID','Form_Name',array('AND Active'=>'= 1'),'Form_ID');
						$str .= form_select($dName,"",$dOptions,$physicalForm,"")
							. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
					$str .= 	'</td>'
						.		'<td width="20%" class="label">'. _LBL_ID_NUMBER .'</td>'
						.		'<td width="30%">'
						;
					
						$dName 		= "idNo$i";
						$dOthers 	= array("type"=>"text");
						$str .= '<span class="ignoreValidate">'. form_input($dName,$idNo,$dOthers) .'</span>'; 
					
					$str .= 	'</td>'
						.	'</tr>'
						.	'<tr class="contents">'
						.		'<td class="label">'. _LBL_NO_OF_INGREDIENT . $requiredField .'</td>'
						.		'<td colspan="3">'
						;
					
						$dName 		= "noIngredient$i";
						$dOthers 	= array("type"=>"text","maxlength"=>"2","class"=>"numbersonly text-center",
										"style"=>"width:30px","title"=>_LBL_MAXLENGTH_2 ."<br />". _LBL_NUMBER_ONLY);
						$str .= form_input($dName,$noIngredient,$dOthers)
							. "&nbsp;"
							. form_button("gomixture",_LBL_GO,array("type"=>"button"))
							. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
					$str .= 	'</td>'
						.	'</tr>'
						;
					
					$str .= '<tr class="contents cls-ingredient" style="display:'. (($noIngredient>0)?"":"none") .'">'
						.		'<td colspan="4"><table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="ingredient-tbl'. $i .'">'
						.			'<thead><tr class="contents">'
						.				'<td class="label" width="3%">'. _LBL_NUMBER .'</td>'
						.				'<td class="label" width="37%">'. _LBL_CHEMICAL_NAME .'</td>'
						.				'<td class="label" width="10%">'. _LBL_CAS_NO .'</td>'
						.				'<td class="label" width="10%">'. _LBL_COMPOSITION .'</td>'
						.				'<td class="label" width="20%">'. _LBL_CHEMICAL_SOURCE .'</td>'
						.				'<td class="label" width="5%"><button class="btn btn-info btn-xs cls-add-row" title="'. _LBL_ADD_INGREDIENT .'"><span class="glyphicon glyphicon-plus"></span></button></td>'
						.			'</tr></thead>'
						;
					$str .= 		'<tbody>';
					
					$j = 1;
					foreach($arrData2 as $detail){
				
						$subChemID	 	= $detail["Sub_Chemical_ID"];
						$chemicalID 	= $detail["Chemical_ID"];
						$chemicalName 	= !empty($detail["Sub_Chemical_Name"])?$detail["Sub_Chemical_Name"]
																			:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
						$casNo			= !empty($detail["Sub_Chemical_CAS"])?$detail["Sub_Chemical_CAS"]
																			:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
						$source		 	= $detail["Sub_Chemical_Source"];
						$composition 	= $detail["Sub_Chemical_Composition"];
						$nolonger		= func_ymd2dmy($detail["StopDate"]);
						
						$str .=		 '<tr class="contents" style="vertical-align:middle">'
							.			'<td align="center">'. $j .'</td>'
							.			'<td>';
							
						$str .= form_input("chemicalID_". $i ."_". $j,$chemicalID,array("type"=>"hidden"));
						$str .= form_input("subChemID_". $i ."_". $j,$subChemID,array("type"=>"hidden"));
						
						$dName 		= "chemicalName_". $i ."_". $j;
						$dOthers 	= array("type"=>"text", "readonly"=>"readonly", "style"=>"width:98%","class"=>"getMixChemical");
						$str .= form_input($dName,$chemicalName,$dOthers)
							. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';

						$str .= 		'</td>'
							.			'<td align="center">';
									
						$dName	 	= "casNo_". $i ."_". $j;
						$dOthers 	= array("type"=>"text","readonly"=>"readonly","style"=>"width:96%;text-align:center;","class"=>"getMixChemical");
						$str .= '<span class="ignoreValidate">'.form_input($dName,$casNo,$dOthers).'</span>'; 
								
						$str .= 		'</td>'
							.			'<td align="center">';
						
						$composition1 = $composition2 = "";
						$dOthers1 = $dOthers2 = array();
						if(!empty($composition)){
							if(strpos($composition,".") === false){ // not found the pointer
								$composition1 = $composition;
								$dOthers2 = array("disabled"=>"disabled");
							}
							else{ // found the pointer
								$composition2 = $composition;
								$dOthers1 = array("disabled"=>"disabled");
							}
						}
						
						$dName 		= "composition_". $i ."_". $j;
						$dOptions 	= $arrComposition;
						$dOthers 	= array("class"=>"eitherOne");
						if(count($dOthers1)>0) $dOthers = array_merge($dOthers,$dOthers1);
						$str .= form_select($dName,"",$dOptions,$composition1,$dOthers);
						$str .= '<br />'. _LBL_OR .'<br />';
							
						$dName 		= "composition2_". $i ."_". $j;
						$dOthers 	= array("type"=>"text","style"=>"width:60%;text-align:center;","class"=>"numbers3decimals eitherOne");
						if(count($dOthers2)>0) $dOthers = array_merge($dOthers,$dOthers2);
						$str .= form_input($dName,$composition2,$dOthers) . '%'; 
						$str .= '<div class="error"><label for="'. $dName .'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
								
						$str .= 		'</td>'
							.			'<td>';
								
						$dName = "source_". $i ."_". $j;
						// $dOptions = $arrSource;
                        // $str .= form_select($dName,"",$dOptions,$source,array("class"=>"slSource")) ;
						
						
						
						
                        // linuxhouse_20220614
                        // start penambahbaikan - 8_ruangan chemical source - v004 1
                        
                        // get option value from arrayCommon.php
                        $dOptions = $arrSource;
                        // gets third element of array
                        $dOptions = $dOptions[3];
                        // push one array into another
                        $new_arr_source = array();
                        array_push($new_arr_source, $dOptions);// push from right to left
                        // disabled attributes
                        $other_attribute = array("disabled"=>"disabled"); 
                        // call to the select option function 2
                        $str .= form_select2($dName,"",$new_arr_source,"",$other_attribute);
                        // gets the value of the 'No Information' array
                        $ni_value = $dOptions['value'];
                        // place hidden input field to contain the value of the disabled select option
                        $str .= form_input($dName, $ni_value, array("type"=>"hidden"));
                        
                        // end penambahbaikan - 8_ruangan chemical source - v004 1
						
						
						

						// $str .= form_radio($dName,$dOptions,$source,"<br />",array("class"=>"rbSource"));
						$str .= '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
								
						$str .= 		'</td>'
							.			'<td class="cls-delete">';
								
						if($statusID<10){ $func_js = "emptyRow('".$i."','".$j."',true)"; $imgName = "delete.gif"; }
						else{ $func_js = ""; $imgName = "delete_0.gif"; }
						//========================================================
						$dImage = $sys_config["images_path"] ."icons/$imgName";
						$dLabel = _LBL_DELETE;
						// $imgParam3 = $func_js;
						// $str .= _get_imagebutton($dImage,$dLabel,""); 
						$str .= '<button class="btn btn-warning btn-xs cls-delete-row" title="'. _LBL_REMOVE_INGREDIENT .'"><span class="glyphicon glyphicon-remove"></span></button>'; 
						
						$str .= 		'</td>'
							.		'</tr>';
													
						$j++;
					}
					$str .= 		'</tbody>';
					$str .= 	'</table></td>'
						.	'</tr>';

					}
				
    // linuxhouse_20211107
    // adding footnote 
				$str .= 	'<tr class="contents cls-hazard" >'
					.			'<td class="label  nil_submission_addedit_04" >'. _LBL_PHY_HAZARD_CLASS . $requiredField . $footNotePhysicalHazardClassification .'</td>'
					.			'<td colspan="3" valign="middle">';
					/************/
					$dName2 	= "selectTo_PH_$i";
					$dValues2 	= $selectTo_PH;
					/************/
					$ids = "0";
					foreach($dValues2 as $id){ if($id!=""){ $ids .= ",".$id; }}
					/************/
					$dOptions2 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"IN ($ids)")
										//,"",TRUE
								);
					$dOthers2 = array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					/************/
					$dName1 	= "selectFrom_PH_$i";
					$dOptions1 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
								"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
								array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"NOT IN (".$ids.")")
								//,"",TRUE
							);
					$dValues1 	= "";
					$dOthers1 	= array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					if($classified==1) $dOthers1 = array_merge($dOthers1,array("disabled"=>"disabled"));
					/************/
					$str .= 	'<table width="100%"><tr><td width="45%">';
					$str .= 		'<span class="slt_right select-multi-overflow">'.form_select2($dName1,"",$dOptions1,$dValues1,$dOthers1).'</span>';
					/************/
					$str .= 		'</td><td width="10%" align="center">'.
									'<span class="enableSelect" style="display:'. (($classified==0)?"":"none;") .'">'.
									'<a href="#" class="choose" rel="From_PH-To_PH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br />'.
									'<a href="#" class="remove" rel="To_PH-From_PH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a>'.
									'</span>'.
									'<span class="disableSelect" style="display:'. (($classified!=0)?"":"none;") .'">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</span>';
					$str .= 		'</td><td width="45%">';
					/************/
					$str .= 		'<span class="slt_left">'. form_multiselect($dName2,"",$dOptions2,$dValues2,$dOthers2) .'</span>';
					$str .= 		'<div class="error"><label for="'.$dName2.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					/************/
					$str .= 	'</td></tr></table>';
					/************/
					$str .= form_input("PH_$i","",array("type"=>"hidden","class"=>"dummy"));

    // linuxhouse_20211107
    // adding footnote 

					$str .= 	'</td>'
						.	'</tr>'
						.	'<tr class="contents cls-hazard">'
						.		'<td class="label">'. _LBL_HEALTH_HAZARD_CLASS . $requiredField . $footNoteHealthHazardClassification . '</td>'
						.		'<td colspan="3" valign="middle">';
					/************/
					$dName2 	= "selectTo_HH_$i";
					$dValues2 	= $selectTo_HH;
					/************/
					$ids = "0";
					foreach($dValues2 as $id){ if($id!=""){ $ids .= ",".$id; }}
					/************/
					$dOptions2 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"IN ($ids)")
										//,"",TRUE
								);
					$dOthers2 = array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					/************/
					$dName1 	= "selectFrom_HH_$i";
					$dOptions1 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
								"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
								array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"NOT IN (".$ids.")")
								//,"",TRUE
							);
					$dValues1 	= "";
					$dOthers1 	= array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					if($classified==1) $dOthers1 = array_merge($dOthers1,array("disabled"=>"disabled"));
					/************/
					$str .= 	'<table width="100%"><tr><td width="45%">';
					$str .= 		'<span class="slt_right">'.form_select2($dName1,"",$dOptions1,$dValues1,$dOthers1).'</span>';
					/************/
					$str .= 		'</td><td width="10%" align="center">'.
									'<span class="enableSelect" style="display:'. (($classified==0)?"":"none;") .'">'.
									'<a href="#" class="choose" rel="From_HH-To_HH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br />'.
									'<a href="#" class="remove" rel="To_HH-From_HH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a>'.
									'</span>'.
									'<span class="disableSelect" style="display:'. (($classified!=0)?"":"none;") .'">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</span>';
					$str .= 		'</td><td width="45%">';
					/************/
					$str .= 		'<span class="slt_left">'. form_multiselect($dName2,"",$dOptions2,$dValues2,$dOthers2) .'</span>';
					$str .= 		'<div class="error"><label for="'.$dName2.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					/************/
					$str .= 	'</td></tr></table>';
					/************/
					$str .= form_input("HH_$i","",array("type"=>"hidden","class"=>"dummy"));

    // linuxhouse_20211107
    // adding footnote 

					$str .= 	'</td>'
						.	'</tr>'
						.	'<tr class="contents cls-hazard">'
						.		'<td class="label">'. _LBL_ENV_HAZARD_CLASS . $requiredField . $footNoteEnvironmentHazardClassification . '</td>'
						.		'<td colspan="3" valign="middle">';
					/************/
					$dName2 	= "selectTo_EH_$i";
					$dValues2 	= $selectTo_EH;
					/************/
					$ids = "0";
					foreach($dValues2 as $id){ if($id!=""){ $ids .= ",".$id; }}
					/************/
					$dOptions2 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"IN ($ids)")
										//,"",TRUE
								);
					$dOthers2 = array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					/************/
					$dName1 	= "selectFrom_EH_$i";
					$dOptions1 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
								"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
								array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"NOT IN (".$ids.")")
								//,"",TRUE
							);
					$dValues1 	= "";
					$dOthers1 	= array("style"=>"width:100%","size"=>"4", 'class' => 'cls-show-detail');
					if($classified==1) $dOthers1 = array_merge($dOthers1,array("disabled"=>"disabled"));
					/************/
					$str .= 	'<table width="100%"><tr><td width="45%">';
					$str .= 		'<span class="slt_right">'.form_select2($dName1,"",$dOptions1,$dValues1,$dOthers1).'</span>';
					/************/
					$str .= 		'</td><td width="10%" align="center">'.
									'<span class="enableSelect" style="display:'. (($classified==0)?"":"none;") .'">'.
									'<a href="#" class="choose" rel="From_EH-To_EH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br />'.
									'<a href="#" class="remove" rel="To_EH-From_EH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a>'.
									'</span>'.
									'<span class="disableSelect" style="display:'. (($classified!=0)?"":"none;") .'">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</span>';
					$str .= 		'</td><td width="45%">';
					/************/
					$str .= 		'<span class="slt_left">'. form_multiselect($dName2,"",$dOptions2,$dValues2,$dOthers2) .'</span>';
					$str .= 		'<div class="error"><label for="'.$dName2.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					/************/
					$str .= 	'</td></tr></table>';
					/************/
					$str .= form_input("EH_$i","",array("type"=>"hidden","class"=>"dummy"));
					$str .= 	'</td>'
						.	'</tr>';
						
					
					
					if($levelID!=7){ // == $levelID = 6/8
						if($levelID==8) $msgDefault = _LBL_MSG_REQUIRED_DEFAULT_IMPORTED;
						else $msgDefault = "";


    // linuxhouse_20211107
    // adding footnote 
						
						$str .= '<tr class="contents cls-import">'
							.		'<td class="label">'. _LBL_TOTAL_QUANTITY_SUPPLY . $requiredField . $footNoteQuantityImported .'</td>'
							.		'<td colspan="3">';
						
						$dName 		= "totalSupply$i";
						$dOptions 	= array("type"=>"text","class"=>"numberspointer cls-totalquantity","title"=>_LBL_NUMBER_ONLY);
						$str .= form_input($dName,$totalSupply,$dOptions) ." ". _LBL_TONNE_YEAR ." $msgDefault"; 
						$str .= 	'<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						// $str .= 	'<span class="error"><label for="'.$dName.'" class="error"></label></span>';
						$str .= 	'</td>'
							.	'</tr>';
					}
					if($levelID!=6){ // == $levelID = 7/8
						if($levelID==8) $msgDefault = _LBL_MSG_REQUIRED_DEFAULT_MANUFACTURED;
						else $msgDefault = "";

    // linuxhouse_20211107
    // adding footnote 
						
						$str .= '<tr class="contents cls-manufacture"> '
							.		'<td class="label">'. _LBL_TOTAL_QUANTITY_USE . $requiredField . $footNoteQuantityManufactured . ' </td>'
							.		'<td colspan="3">';
						
						$dName = "totalUse$i";
						$dOptions = array("type"=>"text","class"=>"numberspointer cls-totalquantity","title"=>_LBL_NUMBER_ONLY);
						$str .= form_input($dName,$totalUse,$dOptions) ." ". _LBL_TONNE_YEAR ." $msgDefault"; 
						$str .= 	'<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						// $str .= 	'<span class="error"><label for="'.$dName.'" class="error"></label></span>';
						$str .= 	'</td>'
							.	'</tr>';
					}
					$str .= '</table>';
				}
			
			}
			echo $str;
			// $arr = array(
					// "dData" => $str
				// );
			// echo json_encode($arr);
		}
		elseif($_POST["fn"]=="mixturechemical"){
			$detailID 	= isset($_POST["detailID"]) ? $_POST["detailID"] 	: "";
			$row 		= isset($_POST["row"]) 		? $_POST["row"] 		: "";
			$mixture	= isset($_POST["mixture"]) 	? $_POST["mixture"] 	: "";
			
			$str = "";
			$i = 1;
			$statusID = 1;
			
			$dataColumns = "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
			$dataTable = "tbl_Submission_Chemical";
			$dataJoin = "";
			$dataWhere = array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
			foreach($arrayData as $detail){
				
				$subChemID	 	= $detail["Sub_Chemical_ID"];
				$chemicalID 	= $detail["Chemical_ID"];
				$chemicalName 	= !empty($detail["Sub_Chemical_Name"])?$detail["Sub_Chemical_Name"]
																	:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
				$casNo			= !empty($detail["Sub_Chemical_CAS"])?$detail["Sub_Chemical_CAS"]
																	:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
				$source		 	= $detail["Sub_Chemical_Source"];
				$composition 	= $detail["Sub_Chemical_Composition"];
				$nolonger		= func_ymd2dmy($detail["StopDate"]);
				
				$str .= '<tr class="contents" style="vertical-align:middle">'.
						'<td align="center">'. $i .'</td>'.
						'<td>';
					
				$str .= form_input("chemicalID_". $row ."_". $i,$chemicalID,array("type"=>"hidden"));
				$str .= form_input("subChemID_". $row ."_". $i,$subChemID,array("type"=>"hidden"));
				
				$dName 		= "chemicalName_". $row ."_". $i;
				$dOthers 	= array("type"=>"text", "readonly"=>"readonly", "style"=>"width:98%","class"=>"getMixChemical");
				$str .= form_input($dName,$chemicalName,$dOthers)
					. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';

				$str .= '</td>'.
						'<td align="center">';
							
				$dName	 	= "casNo_". $row ."_". $i;
				$dOthers 	= array("type"=>"text","readonly"=>"readonly","style"=>"width:96%;text-align:center;","class"=>"getMixChemical");
				$str .= '<span class="ignoreValidate">'.form_input($dName,$casNo,$dOthers).'</span>'; 
						
				$str .= '</td>'.
						'<td align="center">';
				
				$composition1 = $composition2 = "";
				// $dOptions1 = $dOptions2 = array();
				$dOthers1 = $dOthers2 = array();
				if(!empty($composition)){
					if(strpos($composition,".") === false){ // not found the pointer
						$composition1 = $composition;
						$dOthers2 = array("disabled"=>"disabled");
					}
					else{ // found the pointer
						$composition2 = $composition;
						$dOthers1 = array("disabled"=>"disabled");
					}
				}
				
				$dName 		= "composition_". $row ."_". $i;
				$dOptions 	= $arrComposition;
				$dOthers 	= array("class"=>"eitherOne");
				if(count($dOthers1)>0) $dOthers = array_merge($dOthers,$dOthers1);
				$str .= form_select($dName,"",$dOptions,$composition1,$dOthers);
				$str .= '<br />'. _LBL_OR .'<br />';
					
				$dName 		= "composition2_". $row ."_". $i;
				$dOthers 	= array("type"=>"text","style"=>"width:60%;text-align:center;","class"=>"numbers3decimals eitherOne");
				if(count($dOthers2)>0) $dOthers = array_merge($dOthers,$dOthers2);
				$str .= form_input($dName,$composition2,$dOthers) . '%'; 
				$str .= '<div class="error"><label for="'. $dName .'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
				$str .= '</td>'.
						'<td>';
						
				$dName = "source_". $row ."_". $i;
				//$dOptions = $arrSource;
                // $str .= form_select($dName,"",$dOptions,$source,array("class"=>"slSource")) ;
                
                
				
				
				// linuxhouse_20220614
				// start penambahbaikan - 8_ruangan chemical source - v004 2

                // get option value from arrayCommon.php
                $dOptions = $arrSource;
                // gets third element of array
                $dOptions = $dOptions[3];
                // push one array into another
                $new_arr_source = array();
                array_push($new_arr_source, $dOptions);// push from right to left
                // disabled attributes
                $other_attribute = array("disabled"=>"disabled"); 
                // call to the select option function 2
                $str .= form_select2($dName,"",$new_arr_source,"",$other_attribute);
                // gets the value of the 'No Information' array
                $ni_value = $dOptions['value'];
                // place hidden input field to contain the value of the disabled select option
                $str .= form_input($dName, $ni_value, array("type"=>"hidden"));
				
				// end penambahbaikan - 8_ruangan chemical source - v004 2
				
				
				

				// $str .= form_radio($dName,$dOptions,$source,"<br />",array("class"=>"rbSource"));
				$str .= '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
				$str .= '</td>'.
						'<td class="cls-delete">';
						
				if($statusID<10){ $func_js = "emptyRow('".$row."','".$i."',true)"; $imgName = "delete.gif"; }
				else{ $func_js = ""; $imgName = "delete_0.gif"; }
				//========================================================
				$dImage = $sys_config["images_path"] ."icons/$imgName";
				$dLabel = _LBL_DELETE;
				// $imgParam3 = $func_js;
				// $str .= _get_imagebutton($dImage,$dLabel,""); 
				$str .= '<button class="btn btn-warning btn-xs cls-delete-row" title="'. _LBL_REMOVE_INGREDIENT .'"><span class="glyphicon glyphicon-remove"></span></button>'; 
				
				$str .= '</td>'.
					'</tr>';
					
				
				$i++;
			}
			
			for(;$i<=$mixture;$i++){
				$str .= '<tr class="contents" style="vertical-align:middle">'.
						'<td align="center">'. $i .'</td>'.
						'<td>';
					
				$dName = "chemicalID_". $row ."_". $i;
				$str .= form_input($dName,"",array("type"=>"hidden"));
				
				$dName 		= "chemicalName_". $row ."_". $i;
				$dOthers 	= array("type"=>"text", "readonly"=>"readonly", "style"=>"width:98%","class"=>"getMixChemical");
				$str .= form_input($dName,"",$dOthers)
					. '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';

				$str .= '</td>'.
						'<td align="center">';
							
				$dName	 	= "casNo_". $row ."_". $i;
				$dOthers 	= array("type"=>"text","readonly"=>"readonly","style"=>"width:96%;text-align:center;","class"=>"getMixChemical");
				$str .= '<span class="ignoreValidate">'.form_input($dName,"",$dOthers).'</span>'; 
						
				$str .= '</td>'.
						'<td align="center">';
						
				$dName 		= "composition_". $row ."_". $i;
				$dOptions = $arrComposition;
				$dOthers = array("class"=>"eitherOne");
				$str .= form_select($dName,"",$dOptions,"",$dOthers);
				$str .= '<br />'. _LBL_OR .'<br />';
						
				$dName 		= "composition2_". $row ."_". $i;
				$dOthers 	= array("type"=>"text","style"=>"width:60%;text-align:center;","class"=>"numbers3decimals eitherOne");
				$str .= form_input($dName,"",$dOthers) . '%'; 
				$str .= '<div class="error"><label for="'. $dName .'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
				$str .= '</td>'.
						'<td>';
						
				$dName = "source_".$row."_".$i;
				// $dOptions = $arrSource;
                // $str .= form_radio($dName,$dOptions,"1","<br />",array("class"=>"rbSource"));
				
				
				
				
				// linuxhouse_20220614
				// start penambahbaikan - 8_ruangan chemical source - v004 3

                // get option value from arrayCommon.php
                $dOptions = $arrSource;
                // gets third element of array
                $dOptions = $dOptions[3];
                // push one array into another
                $new_arr_source = array();
                array_push($new_arr_source, $dOptions);// push from right to left
                // disabled attributes
                $other_attribute = array("disabled"=>"disabled"); 
                // call to the select option function 2
                $str .= form_select2($dName,"",$new_arr_source,"",$other_attribute);
                // gets the value of the 'No Information' array
                $ni_value = $dOptions['value'];
                // place hidden input field to contain the value of the disabled select option
                $str .= form_input($dName, $ni_value, array("type"=>"hidden"));
				
                // end penambahbaikan - 8_ruangan chemical source - v004 3
				
				
				
				
				$str .= '<div class="error"><label for="'.$dName.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
				$str .= '</td>'.
						'<td class="cls-delete">';
						
				if($statusID<10){ $func_js = "emptyRow('".$row."','".$i."',true)"; $imgName = "delete.gif"; }
				else{ $func_js = ""; $imgName = "delete_0.gif"; }
				//========================================================
				$dImage = $sys_config["images_path"] ."icons/$imgName";
				$dLabel = _LBL_DELETE;
				// $imgParam3 = $func_js;
				// $str .= _get_imagebutton($dImage,$dLabel,"");
				$str .= '<button class="btn btn-warning btn-xs cls-delete-row" title="'. _LBL_REMOVE_INGREDIENT .'"><span class="glyphicon glyphicon-remove"></span></button>'; 
				
				$str .= '</td>'.
					'</tr>';
					
					
                
                
				// linuxhouse_20220614
				// start penambahbaikan - 8_ruangan chemical source - v004 4
                $str .= "
                        <script>
                            // disables the first chemical source item when the table loads
                            $('#$dName').prop('disabled', true);
                        </script>
                         ";	
                // end penambahbaikan - 8_ruangan chemical source - v004 4
                
                
                
                         
			}
			
			$arr = array(
					"dData" => $str
				);
			echo json_encode($arr);
			
		}
		elseif($_POST["fn"]=="hazardclass"){
			//bahagian ini akan di post ke form selepa silihan .. update from dosh staff

			$id 	= isset($_POST["id"]) 	? $_POST["id"] 		: "";
			$row 	= isset($_POST["row"]) 	? $_POST["row"] 	: "";
			$type 	= isset($_POST["type"]) ? $_POST["type"]	: "";
			
			

			$hazard = _get_StrFromManyConditions("tbl_chemical_classified","Category_ID",array("AND Chemical_ID"=>" = ". quote_smart($id),"AND Active"=>" = 1"));
			$fromPH = $toPH = "";
			$fromHH = $toHH = "";
			$fromEH = $toEH = "";
			
			if($type==1){
				//setelah pilihan di buat substance
				if(empty($id)) $hazard = "";
			
				if(!empty($hazard)){
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}	
				
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
								);				
					foreach($dOptions as $dOpts){
						$toPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}							
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
								);				
					foreach($dOptions as $dOpts){
						$toHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}						
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
								);				
					foreach($dOptions as $dOpts){
						$toEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					
				}
				else{
					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 1","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					
					//query hazard dari sejarah pengguna
					$getPH = _get_datalama("Category_ID_ph",$id,$_SESSION['user']['Usr_ID']);
					//$getPH = "1,2,3,4,5,6,7,8";

					if (!empty($getPH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"IN ($getPH)"),"",TRUE
						);	

						foreach($dOptions as $dOpts){
							//ubah di sini 
							$toPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 2","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					//ambil data dari sejarah 
					$getHH = _get_datalama("Category_ID_hh",$id,$_SESSION['user']['Usr_ID']);
					if(!empty($getHH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"IN ($getHH)"),"",TRUE
									);				
						foreach($dOptions as $dOpts){
							$toHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}

					
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 3","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					//ambil data dari sejarah 
					$getEH = _get_datalama("Category_ID_eh",$id,$_SESSION['user']['Usr_ID']);
					if(!empty($getEH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"IN ($getEH)"),"",TRUE
									);				
						foreach($dOptions as $dOpts){
							$toEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}
					//kemasukan baru , sistem akan mencari rekod lama untuk di masukkan by staff dosh
					
					
				}
			}
			
			if(!empty($hazard)){
				
				$dviewx = "1";
			}
			else{
				if(empty($getEH) AND empty($getHH) AND empty($getPH) ){
					$dviewx = "0";
				}
				else{ 
					$dviewx = "2";
				}
			}
			
			$arr = array(
				    //"classified" => ( !empty($hazard) ? "1" : "0" ),
					//"classified" => ( !empty($hazard) ? "1" : "1" ),
					"classified" => $dviewx,
					"row" => $row,
					"fromPH" => $fromPH,
					"toPH" => $toPH,
					"fromHH" => $fromHH,
					"toHH" => $toHH,
					"fromEH" => $fromEH,
					"toEH" => $toEH
				);
			echo json_encode($arr);
		}
		elseif($_POST["fn"]=="duplicate"){
			$msg = "";
			$submissionID = isset($_POST["submissionID"]) ? $_POST["submissionID"] : "";
			
			$dColumns 	= "Usr_ID,Type_ID";
			$dTable 	= "tbl_Submission";
			$dJoin 		= "";
			$dWhere 	= array("AND Submission_ID" => "= ".quote_smart($submissionID));
			$arrData 	= _get_arrayData($dColumns,$dTable,$dJoin,$dWhere); 
			foreach($arrData as $detail){
				
				$dData = array(
							"Usr_ID" => $detail["Usr_ID"],
							"Type_ID" => $detail["Type_ID"],
							"Status_ID" => "1",
							"BulkSubmission" => "N",
							"RecordBy" => $_SESSION["user"]["Usr_ID"],
							"RecordDate" => date("Y-m-d H:i:s")
						);
						
				$sqlIns = "INSERT INTO $dTable (";
				foreach($dData as $column => $value){
					$sqlIns .= "$column,";
				}
				$sqlIns = substr_replace( $sqlIns, "", -1 );
				$sqlIns .= ") VALUES (";
				foreach($dData as $value){
					$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
				}
				$sqlIns = substr_replace( $sqlIns, "", -1 );
				$sqlIns .= ")";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$subID = $db->sql_nextid();
				
				if($subID>0){
					$msg = _LBL_DUPLICATION_SUCCESS;
				
					$dColumns2 	= "Detail_ID,Detail_ProductName,Detail_ID_Number,Form_ID,Category_ID_ph,Category_ID_hh,Category_ID_eh,Detail_TotalSupply,Detail_TotalUse";
					$dTable2 	= "tbl_Submission_Detail";
					$dJoin2 	= "";
					$dWhere2 	= array("AND Submission_ID" => "= ".quote_smart($submissionID));
					$arrData2 	= _get_arrayData($dColumns2,$dTable2,$dJoin2,$dWhere2); 
					foreach($arrData2 as $detail2){
						
						$dData2 = array(
									"Detail_ProductName" => $detail2["Detail_ProductName"],
									"Detail_ID_Number" => $detail2["Detail_ID_Number"],
									"Form_ID" => $detail2["Form_ID"],
									"Category_ID_ph" => $detail2["Category_ID_ph"],
									"Category_ID_hh" => $detail2["Category_ID_hh"],
									"Category_ID_eh" => $detail2["Category_ID_eh"],
									"Detail_TotalSupply" => $detail2["Detail_TotalSupply"],
									"Detail_TotalUse" => $detail2["Detail_TotalUse"],
									"Submission_ID" => $subID,
									"RecordDate" => date("Y-m-d H:i:s")
								);
						
						$sqlIns = "INSERT INTO $dTable2 (";
						foreach($dData2 as $column => $value){
							$sqlIns .= "$column,";
						}
						$sqlIns = substr_replace( $sqlIns, "", -1 );
						$sqlIns .= ") VALUES (";
						foreach($dData2 as $value){
							$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
						}
						$sqlIns = substr_replace( $sqlIns, "", -1 );
						$sqlIns .= ")";
						$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
						$detailID = $db->sql_nextid();
					
						/**************
						 *
						 **************/
						$dColumns3 	= "Sub_Chemical_Name,Sub_Chemical_CAS,Sub_Chemical_Composition,Sub_Chemical_Source,Chemical_ID";
						$dTable3 	= "tbl_Submission_Chemical";
						$dJoin3 	= "";
						$dWhere3 	= array("AND Detail_ID" => "= ".quote_smart($detail2["Detail_ID"]));
						$arrData3 	= _get_arrayData($dColumns3,$dTable3,$dJoin3,$dWhere3); 
						foreach($arrData3 as $detail3){
							$dData3 = array(
										"Sub_Chemical_Name" => $detail3["Sub_Chemical_Name"],
										"Sub_Chemical_CAS" => $detail3["Sub_Chemical_CAS"],
										"Sub_Chemical_Composition" => $detail3["Sub_Chemical_Composition"],
										"Sub_Chemical_Source" => $detail3["Sub_Chemical_Source"],
										"Chemical_ID" => $detail3["Chemical_ID"],
										"Detail_ID" => $detailID,
										"RecordDate" => date("Y-m-d H:i:s")
									);
						
							$sqlIns = "INSERT INTO $dTable3 (";
							foreach($dData3 as $column => $value){
								$sqlIns .= "$column,";
							}
							$sqlIns = substr_replace( $sqlIns, "", -1 );
							$sqlIns .= ") VALUES (";
							foreach($dData3 as $value){
								$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
							}
							$sqlIns = substr_replace( $sqlIns, "", -1 );
							$sqlIns .= ")";
							$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
							$chemicalID = $db->sql_nextid();
									
						}
						/***************/
						
					}
					
				}
				else{ $msg = _LBL_DUPLICATION_FAILED; }
			}
			$arr = array(
					"result" => $msg,
                                        "newid"  => $subID
				);
			echo json_encode($arr);
			
		}
		elseif($_POST["fn"]=="checking"){
			$Usr_ID			= isset($_POST["Usr_ID"])?$_POST["Usr_ID"]:"";
			$submissionID	= isset($_POST["submissionID"])?$_POST["submissionID"]:"";
			$subSubmitID	= isset($_POST["subSubmitID"])?$_POST["subSubmitID"]:"";
			$statusID 		= isset($_POST["approved"])?$_POST["approved"]:0;
			$checkRemark 	= isset($_POST["checkRemark"])?$_POST["checkRemark"]:"";
			$checkReason 	= isset($_POST["checkReason"])?$_POST["checkReason"]:"";
			$checkContact 	= isset($_POST["checkContact"])?$_POST["checkContact"]:"";
			
			/****************************
			 * add /R for reject status; update on 20141031
			 ****************************/
			if($statusID=="2"){
				if(!empty($subSubmitID)){
					$ar_subNo	= explode("/",$subSubmitID);
					if(is_numeric($ar_subNo[ count($ar_subNo)-1 ]))
						$indexLast = count($ar_subNo);
					else $indexLast = count($ar_subNo)-1;
						
					$ar_subNo[ $indexLast ] = "R";
					$subSubmitID = implode("/",$ar_subNo);
				}
			}
			/****************************/
			
			$sqlUpd = "UPDATE tbl_Submission SET"
					/****************************
					 * update on 20141031
					 ****************************/
					. " Submission_Submit_ID = ". quote_smart($subSubmitID) .","
					/****************************/
					. " Status_ID = ". quote_smart($statusID) .","
					. " CheckedBy = ". quote_smart($Usr_ID) .","
					. " CheckedDate = NOW(),"
					. " CheckedRemark = ". quote_smart($checkRemark) .","
					. " CheckedReason = ". quote_smart($checkReason) .","
					. " CheckedContact = ". quote_smart($checkContact) .""
					. " WHERE Submission_ID = ".quote_smart($submissionID)." LIMIT 1";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resUpd){
				if($statusID=="21") $msg = "Submission was sent for approval.";
				elseif($statusID=="2") $msg = "Submission was sent back to the supplier.";
				else $msg = "NA";
				/*********/
				$sqlIns = "INSERT INTO tbl_Submission_Record ("
						. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,Sub_Record_Contact,"
						. " Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID"
						. " ) VALUE ("
						. " ".quote_smart($msg).",".quote_smart($checkRemark).",".quote_smart($checkReason).",".quote_smart($checkContact).","
						. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($statusID).",".quote_smart($submissionID)
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*********/
				if($statusID=="2"){
					$usrid = _get_StrFromCondition("tbl_Submission","Usr_ID","Submission_ID",$submissionID);
					$content = _get_Notification(7,$usrid,$submissionID); //
					if(is_array($content)){
						if($content["email"]!=""){
							func_email2($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
						}
					}
             }
				/*********/
			}else{ $msg = "Submission cannot sent for approval"; }
			func_add_audittrail($Usr_ID,$msg." [".$submissionID."]","Submission","tbl_Submission,".$submissionID);
			/*********/
			$arr = array(
						"result" => $msg
					);
			echo json_encode($arr);
		}
		elseif($_POST["fn"]=="acknowledge"){
			$Usr_ID			= isset($_POST["Usr_ID"])?$_POST["Usr_ID"]:"";
			$submissionID	= isset($_POST["submissionID"])?$_POST["submissionID"]:"";
			$subSubmitID = isset($_POST['subSubmitID'])?$_POST['subSubmitID']:'';
			$statusID 	= isset($_POST['approved'])?$_POST['approved']:0;
			$appRemark	= isset($_POST['appRemark'])?$_POST['appRemark']:'';
			$appReason	= isset($_POST['appReason'])?$_POST['appReason']:'';
			$appContact	= isset($_POST['appContact'])?$_POST['appContact']:'';
			/*********/
			
			/****************************
			 * add /R for reject status; update on 20141031
			 ****************************/
			if($statusID=="2"){
				if(!empty($subSubmitID)){
					$ar_subNo	= explode("/",$subSubmitID);
					if(is_numeric($ar_subNo[ count($ar_subNo)-1 ]))
						$indexLast = count($ar_subNo);
					else $indexLast = count($ar_subNo)-1;
						
					$ar_subNo[ $indexLast ] = "R";
					$subSubmitID = implode("/",$ar_subNo);
				}
			}
			// echo $subSubmitID;die;
			/****************************/
			
			$newsubSubmitID = $subSubmitID;
			/*********/		
			$sqlUpd = "UPDATE tbl_Submission SET";
			$sqlUpd .= " Submission_Submit_ID = ". quote_smart($newsubSubmitID) .",";
			$sqlUpd .= " Status_ID = ". quote_smart($statusID) .","
					. " ApprovedBy = ". quote_smart($Usr_ID) .","
					. " ApprovedDate = NOW(),"
					. " ApprovedRemark = ". quote_smart($appRemark) .","
					. " ApprovedReason = ". quote_smart($appReason) .","
					. " ApprovedContact = ". quote_smart($appContact) .""
					. " WHERE Submission_ID = ".quote_smart($submissionID)." LIMIT 1";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resUpd){
				if($statusID=="31"){
					/*********/
					$sqlUpd	= "UPDATE tbl_Submission s,tbl_Submission_Detail sd,tbl_Submission_Chemical sc SET"
							. " sc.ApprovedDate = NOW(),sc.UpdateDate = NOW(),"
							. " sd.ApprovedDate = NOW(),sd.UpdateDate = NOW(),"
							. " s.UpdateDate = NOW(),s.UpdateBy = ". quote_smart($Usr_ID) .""
							. " WHERE s.isDeleted = 0 AND sd.isDeleted = 0 AND sc.isDeleted = 0"
							. " AND sc.Detail_ID = sd.Detail_ID AND sd.Submission_ID = s.Submission_ID"
							. " AND sc.ApprovedDate IS NULL AND s.Submission_ID = ".quote_smart($submissionID)."";
							//echo $sqlUpd;die;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					$msg = "Submission was acknowledged.";
					/*********/
					//notification /*********/
					$usrid = _get_StrFromCondition("tbl_Submission","Usr_ID","Submission_ID",$submissionID);
					$content = _get_Notification(2,$usrid,$submissionID); //2 is for new submission
					if(is_array($content)){
						if($content["email"]!=""){
							func_email2($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
						}
					}
					//notification /*********/
				}
				elseif($statusID=="2"){
					$msg = "Submission was sent back to the supplier.";
					/*********/
					$usrid = _get_StrFromCondition("tbl_Submission","Usr_ID","Submission_ID",$submissionID);
					$content = _get_Notification(15,$usrid,$submissionID); //
					if(is_array($content)){
						if($content["email"]!=""){
							func_email2($sys_config["email_admin"],$content["email"],$content["title"],$content["body"]);
						}
					}
					/*********/
				}
				else $msg = "NA";
				/*********/
				$sqlIns = "INSERT INTO tbl_Submission_Record ("
						. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,Sub_Record_Contact,"
						. " Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID"
						. " ) VALUE ("
						. " ".quote_smart($msg).",".quote_smart($appRemark).",".quote_smart($appReason).",".quote_smart($appContact).","
						. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($statusID).",".quote_smart($submissionID)
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*********/
			}
			else{ $msg = "Submission cannot be acknowledged"; }
			func_add_audittrail($Usr_ID,$msg." [".$submissionID."]","Submission","tbl_Submission,".$submissionID);
	
			$arr = array(
						"result" => $msg
					);
			echo json_encode($arr);
		}
		elseif($_POST['fn']=='checkexist'){
			$exist = 0;
			if(isset($_POST['cas'])){
				if($_POST['cas']!=''){					
					$dataColumns = "Chemical_ID, Chemical_Name, Chemical_CAS";
					$dataTable = "tbl_chemical";
					$dataJoin = "";
					$dataWhere = array("AND Chemical_CAS" => "= ".quote_smart($_POST['cas']), 'AND isActive' => '= 1', 'AND isNew' => '= 0', 'AND isDeleted' => '= 0');
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
					foreach($arrayData as $detail){
						$exist = 1;
						$chemical_id = $detail['Chemical_ID'];
						$chemical_name = $detail['Chemical_Name'];
						$chemical_cas = $detail['Chemical_CAS'];
					}
				}
				
			}
									
			$arr = array(
						"chemical_id" => isset($chemical_id) ? $chemical_id : '',
						"chemical_name" => isset($chemical_name) ? $chemical_name : '',
						"chemical_cas" => isset($chemical_cas) ? $chemical_cas : '',
						'exist' => $exist,
					);
			echo json_encode($arr);
		}
	}

<?php
	//============================================================
	// FILE BEGIN 
	// filename: submissionApproved_pdf.php
	// description: submission approved
	//============================================================
	include_once "../includes/sys_config.php";
	/*****/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*****/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*****/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
 	$_tcpdf = "tcpdf/";
	require_once($_tcpdf."config/lang/eng.php");
	require_once($_tcpdf."tcpdf.php");
	/*****/
	if(isset($_GET["submissionID"])) $submissionID = $_GET["submissionID"];
	else $submissionID = 0;
	/*****/
	$coName	= "";
	$coNo	= "";
	$lvlID = "";
	$contactName = $contactNo = $contactEmail = "";
	/*****/
	$subID = "";
	$subDate = "";
	$ackDate = "";
	$typeName = "";
	$typeID = "";
	/*****/
	$langdesc = (($fileLang=="may.php")?"_may":"");
	
	if($submissionID!=0){
			/*****/
			$subID	 = _get_StrFromCondition("tbl_Submission","Submission_Submit_ID","Submission_ID",$submissionID);
			$subDate = _getSubmitDate($submissionID);
			$expDate = _getExpiredDate($submissionID);
			$ackDate = func_ymd2dmy(_get_StrFromCondition("tbl_Submission","ApprovedDate","Submission_ID",$submissionID));
			$usrid 	 = _get_StrFromCondition("tbl_Submission","Usr_ID","Submission_ID",$submissionID);
			$typeID  = _get_StrFromCondition("tbl_Submission","Type_ID","Submission_ID",$submissionID);
			$colTypeName = "Type_Name". $langdesc;
			$typeName = _get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$typeID);
			/*****/
			$statusID = _get_StrFromCondition("tbl_Submission","Status_ID","Submission_ID",$submissionID);
			if(in_array($statusID,array(31,32,33))){
				$rpttypetitle = _LBL_CERT_SUBMISSION_TITLE;
				$rpttype = _LBL_CERT_SUBMISSION;
			}else{
				$rpttypetitle = _LBL_CERT_RESUBMISSION_TITLE;
				$rpttype = _LBL_CERT_RENEW_SUBMISSION;
			}
			/*****/
		if($usrid!=0||$usrid!=""){
			/*****/
			$dataColumns = "u.Level_ID,uc.Company_Name,uc.Company_No,uc.Contact_Name,uc.Contact_Mobile_No,uc.Contact_Email";
			$dataTable = "sys_User u";
			$dataJoin = array("sys_User_Client uc"=>"u.Usr_ID = uc.Usr_ID","sys_Level l"=>"u.Level_ID = l.Level_ID");
			$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($usrid));
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
			/*****/
			foreach($arrayData as $detailData){
				$coName	= strtoupper($detailData["Company_Name"]);
				$coNo	= strtoupper($detailData["Company_No"]);
				//$lvlName = strtoupper($detailData["Level_Name"]);
				$lvlID	= $detailData["Level_ID"];
				
				$contactName 	= strtoupper($detailData["Contact_Name"]);
				$contactNo 		= strtoupper($detailData["Contact_Mobile_No"]);
				$contactEmail 	= $detailData["Contact_Email"];
			}
			/*****/
		}
		/*****/
	}
	/*****/
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*****/
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleSupp, $titleYear, $sys_config, $rpttypetitle;
			
			$this->Ln(10);
			$this->SetFont("helvetica","",9);
			/*****/
			$tbl = '<div align="right"><i>CIMS/CRT/02</i></div>'
				. '<center>'
				. '<table border="0" width="100%" cellspacing="0" cellpadding="2" align="center">'
				. 	'<tr>'
				. 		'<td align="center"><img src="'.$sys_config['images_path'].'jata.gif" height="50" width="70" border="0" /></td>'
				. 	'</tr>'
				. 	'<tr>'
				. 		'<td align="center">'.strtoupper(_LBL_DOSH).'</td>'
				.	'</tr>'
				. 	'<tr>'
				.		'<td align="center">'.strtoupper(_LBL_CIMS).'</td>'
				.	'</tr>'
				.	'<tr>'
				.		'<td align="center">'.strtoupper($rpttypetitle).'</td>'
				.	'</tr>'
				. '</table>'
				. '</center>'
				;
			$this->writeHTML($tbl, true, false, false, false, '');
			//====================================================
			//$this->Ln(2);
			//Ensure table header is output	
			parent::Header();
		}
		
		public function Footer(){
			global $sys_config;
			$this->SetFont("helvetica","",9);
			$this->SetY(-10);
			$this->Cell(0,3,_LBL_PRINTED_ON .": ".date("d-m-Y"),0,0,"L");
			$this->SetY(-10);
			//Page number
			$this->Cell(0,3,_LBL_PAGE ." ". $this->PageNo()."/{nb}",0,0,"R");
			$this->SetY(-40);
			/*****/
			$tbl = '<table border="0" width="100%" cellspacing="0" cellpadding="2">'
				.	'<tr>'
				.		'<td align="right"><img src="'.$sys_config['images_path'].'DOSH_COP.jpg" width="60" border="0" style="opacity:0.6;filter:alpha(opacity=60);" /></td>'
				.	'</tr>'
				. '</table>'
				;
			$this->writeHTML($tbl, true, false, false, false, '');
			/*****/
		}
	}
	/*****/
	$pdf = new MYPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8", false); 
	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(20, 56); //left, top, right
	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	/*****/
	// set font
	$pdf->SetFont("helvetica", "", 8);
	$pdf->AliasNbPages(); 
	$pdf->AddPage();
	
	$lvlName = "";
	foreach($arrLevel as $detail){
		if($detail["value"]==$lvlID){
			$lvlName = $detail["label"]; 
			break; 
		}
	}
	
	$tbl = '<center>'
		.	'<table style="border:1px solid" cellspacing="1" cellpadding="3" width="100%">'
		.		'<tbody>'
		.		'<tr>'
		.			'<td><table border="0" cellspacing="1" cellpadding="5" width="100%">'
		.				'<tbody>'
		.				'<tr>'
		.					'<td colspan="4">&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td width="5%">&nbsp;</td>'
		.					'<td width="25%"><label><strong>'. _LBL_CERT_COMP_NAME .'</strong></label></td>'
		.					'<td width="65%">: <label>'. $coName .'</label></td>'
		.					'<td width="5%">&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label><strong>'. _LBL_CERT_COMP_REG_NO .'</strong></label></td>'
		.					'<td>: <label>'. $coNo .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label><strong>'. _LBL_CERT_TYPE_SUPPIER .'</strong></label></td>'
		.					'<td>: <label>'. $lvlName .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label><strong>'. _LBL_CONTACT_DETAIL .'</strong></label></td>'
		.					'<td>: <label>'. $contactName . (!empty($contactNo)? " (". $contactNo .")" : "") .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label><strong>'. _LBL_EMAIL .'</strong></label></td>'
		.					'<td>: <label>'. $contactEmail .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td colspan="2"><hr size="3" color="black" /><br /><label><strong>'. _LBL_CERT_SUBMISSION_INFO .'</strong></label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td width="5%">&nbsp;</td>'
		.					'<td width="25%"><label>'. _LBL_CERT_SUBMISSION_ID .'</label></td>'
		.					'<td width="65%">: <label>'. $subID .'</label></td>'
		.					'<td width="5%">&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label>'. _LBL_CERT_SUBMISSION_DATE .'</label></td>'
		.					'<td>: <label>'. $subDate .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		// .				'<!--'
		// .				'<tr>'
		// .					'<td>&nbsp;</td>'
		// .					'<td><label>'. _LBL_CERT_EXPIRED_DATE .'</label></td>'
		// .					'<td>: <label>'. $expDate .'</label></td>'
		// .					'<td>&nbsp;</td>'
		// .				'</tr>'
		// .				'-->'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label>'. _LBL_CERT_CHEMICAL_TYPE .'</label></td>'
		.					'<td>: <label>'. $typeName .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td><label>'. _LBL_CERT_ACKNWLG_DATE .'</label></td>'
		.					'<td>: <label>'. $ackDate .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td>&nbsp;</td>'
		.					'<td colspan="2" align="justify"><hr size="3" color="black" /><br />'
		.					'<label>'. _LBL_CERT_SUBMISSION_SUBJ1 .' '.$rpttype.' '. _LBL_CERT_SUBMISSION_SUBJ2 . _LBL_CERT_ORG_INFO .'</label></td>'
		.					'<td>&nbsp;</td>'
		.				'</tr>'
		.				'<tr>'
		.					'<td colspan="4">&nbsp;</td>'
		.				'</tr>'
		.				'</tbody>'
		.			'</table></td>'
		.		'</tr>'
		.		'</tbody>'
		.	'</table>'
		.	'</center>'
		;
	//	echo $tbl;die;
	$pdf->writeHTML($tbl, true, false, false, false, "");
	/*****/
	$label = "color:#000000; background-color:#BFBFBF; font-weight:bold;";
	$label2 = "color:#000000; background-color:#F1F1F1; font-weight:bold;";
	
	$pdf->AddPage();
	$tbl = '<center>'
		. '<table border="1" cellspacing="0" cellpadding="3" width="100%">'
		. 	'<thead>'
		. 	'<tr>'
		.		'<td width="5%" align="center" style="'.$label.'">'. _LBL_NUMBER .'</td>'
		.		'<td width="95%" align="center" colspan="4" style="'.$label.'">'. _LBL_DETAIL .'</td>'
		.	'</tr>'
		.	'</thead>'
		.	'<tbody>'
		;
		/*****/		
		$dColumns 	= "Detail_ID,Detail_ProductName,Detail_ID_Number,Form_ID,Category_ID_ph,Category_ID_hh,Category_ID_eh,"
					. "Detail_TotalSupply,Detail_TotalUse";
		$dTable 	= "tbl_Submission_Detail";
		$dJoin 		= "";
		$dWhere 	= array("AND Submission_ID" => "= ".quote_smart($submissionID),"AND isDeleted"=>"= 0");
		$arrData 	= _get_arrayData($dColumns,$dTable,$dJoin,$dWhere); 
		// extractArray($arrayData);
		/*****/
		$td_width = ($lvlID!=6 || $lvlID!=7) ? "27.5%" : "75%";
		$td_colspan = ($lvlID!=6 || $lvlID!=7) ? '' : ' colspan="3"';
		/*****/
		$i = 0;
		
		$quantity = _get_StrFromCondition("tbl_Submission","Quantity","Submission_ID",$submissionID);
		
		foreach($arrData as $detail){
			$i++;
			
			$totalSupply 	= $detail["Detail_TotalSupply"];
			$totalUse 		= $detail["Detail_TotalUse"];
			
			if($lvlID==6 || $lvlID==7)
				$addrow = 0;
			else $addrow = 1;
			
			if($typeID==1){
			
				$dColumns2 	= "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
				$dTable2 	= "tbl_Submission_Chemical";
				$dJoin2 	= "";
				$dWhere2 	= array("AND Detail_ID" => "= ".quote_smart($detail["Detail_ID"]),"AND isDeleted"=>"= 0");
				$arrData2	= _get_arrayData($dColumns2,$dTable2,$dJoin2,$dWhere2); 
				foreach($arrData2 as $detail2){
					$chemicalID 	= $detail2["Chemical_ID"];
					$chemicalName 	= !empty($detail2["Sub_Chemical_Name"])?$detail2["Sub_Chemical_Name"]
																		:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
					$composition 	= !empty($detail2["Sub_Chemical_Composition"])?$detail2["Sub_Chemical_Composition"]:"-";
					$source		 	= $detail2["Sub_Chemical_Source"];
					$casNo			= !empty($detail2["Sub_Chemical_CAS"])?$detail2["Sub_Chemical_CAS"]
																		:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
					$nolonger		= !empty($detail2["StopDate"])?func_ymd2dmy($detail2["StopDate"]):"-";
					
					$isCWC			= _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$chemicalID);
				}

	$tbl .= '<tr>'
		.		'<td width="5%" align="center" rowspan="'. (4+$addrow) .'">'. $i .'</td>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_PHYSICAL_FORM .'</td>'
		.		'<td width="75%" colspan="3">'. _get_StrFromCondition("tbl_Chemical_Form","Form_Name". $langdesc,"Form_ID",$detail["Form_ID"]) .'</td>'
		.	'</tr>'
		.	'<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_TRADE_PRODUCT .'</td>'
		.		'<td width="27.5%">'. nl2br(func_fn_escape($chemicalName)) .( ($isCWC==1 && $userLevelID<=5)?' <b>[CWC]</b>' : '') .'</td>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_CAS_NO .'</td>'
		.		'<td width="27.5%">'. $casNo .'</td>'
		.	'</tr>'
		;
			}
			elseif($typeID==2){
				$dColumns2 	= "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
				$dTable2 	= "tbl_Submission_Chemical";
				$dJoin2 	= "";
				$dWhere2 	= array("AND Detail_ID" => "= ".quote_smart($detail["Detail_ID"]),"AND isDeleted"=>"= 0");
				$arrData2	= _get_arrayData($dColumns2,$dTable2,$dJoin2,$dWhere2); 

	$tbl .= '<tr>'
		.		'<td width="5%" align="center" rowspan="'. (6+$addrow) .'">'. $i .'</td>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_PRODUCT_NAME .'</td>'
		.		'<td width="75%" colspan="3">'. (($detail["Detail_ProductName"]!="")?$detail["Detail_ProductName"]:"-") .'</td>'
		.	'</tr>'
		.	'<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_PHYSICAL_FORM .'</td>'
		.		'<td width="27.5%">'. _get_StrFromCondition("tbl_Chemical_Form","Form_Name". $langdesc,"Form_ID",$detail["Form_ID"]) .'</td>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_ID_NUMBER .'</td>'
		.		'<td width="27.5%">'. (($detail["Detail_ID_Number"]!="")?$detail["Detail_ID_Number"]:"-") .'</td>'
		.	'</tr>'
		.	'<tr>'
		.		'<td width="95%" style="'.$label2.'" colspan="4">'. _LBL_INGREDIENT .'</td>'
		.	'</tr>'
		.	'<tr>'
		.		'<td width="95%" colspan="4" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="2">'
		.			'<thead>'
		.			'<tr style="'.$label2.'">'
		.				'<td width="6%">'. _LBL_NUMBER .'</td>'
		.				'<td width="47%" align="left">'. _LBL_CHEMICAL_NAME .'</td>'
		.				'<td width="13%">'. _LBL_CAS_NO .'</td>'
		.				'<td width="15%">'. _LBL_COMPOSITION .'</td>'
		.				'<td width="19%">'. _LBL_CHEMICAL_SOURCE .'</td>'
		//.				'<td width="20%">'. _LBL_DATE_NOLONGER .'</td>'
		.			'</tr>'
		.			'</thead>'
		.			'<tbody>'
		;
				/*****/
				/*****/
				$j = 0;
				foreach($arrData2 as $detailChem){
					$j++;
					/*****/
					$source = "";
					foreach($arrSource as $detailSource){
						if($detailSource["value"]==$detailChem["Sub_Chemical_Source"]) 
							$source = $detailSource["label"]; 
					}
					/*****/
					$composition = $detailChem["Sub_Chemical_Composition"];
					if(!empty($composition)){	
						if(strpos($composition,".") === false){ // not found the pointer
							foreach($arrComposition as $comp){
								if($comp["value"]==$composition) $composition = $comp["label"]; 
							}
						}
						else $composition = !empty($composition) ? $composition . "%" : "-"; 
					}
					$isCWC = _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$detailChem["Chemical_ID"]);
					/*****/
	$tbl .= 		'<tr>'
		.				'<td>'. $j .'</td>'
		.				'<td align="left">'. nl2br(func_fn_escape(_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$detailChem["Chemical_ID"]))) 
		. 				( ($isCWC==1 && $userLevelID<=5)?' <b>[CWC]</b>' : '').'</td>'
		.				'<td>'. _get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$detailChem["Chemical_ID"]) .'</td>'
		.				'<td>'. $composition .'</td>'
		.				'<td>'. (!empty($source)?$source:"-") .'</td>'
		//.				'<td>'. (($detailChem["StopDate"]!="")?func_ymd2dmy($detailChem["StopDate"]):"-") .'</td>'
		.			'</tr>'
		;
				}
	$tbl .= 		'</tbody>'
		.		'</table></td>'
		.	'</tr>'
		;			
			}
			/*****/
			$str_catPH = "";
			if($detail["Category_ID_ph"]!=""){
				$ex_catPH = explode(",",$detail["Category_ID_ph"]);
				/*****/
				foreach($ex_catPH as $catPH){
					if($str_catPH!="") $str_catPH .= "<br />";
					$str_catPH 	.= _get_StrFromCondition("tbl_Hazard_Category",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catPH).";";
				}
			}
			/*****/
			$str_catHH = "";
			if($detail["Category_ID_hh"]!=""){
				$ex_catHH = explode(",",$detail["Category_ID_hh"]);
				/*****/
				foreach($ex_catHH as $catHH){
					if($str_catHH!="") $str_catHH .= "<br />";
					$str_catHH 	.= _get_StrFromCondition("tbl_Hazard_Category",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catHH).";";
				}
			}
			/*****/
			$str_catEH = "";
			if($detail["Category_ID_eh"]!=""){
				$ex_catEH = explode(",",$detail["Category_ID_eh"]);
				/*****/
				foreach($ex_catEH as $catEH){
					if($str_catEH!="") $str_catEH .= "<br />";
					$str_catEH 	.= _get_StrFromCondition("tbl_Hazard_Category",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catEH).";";
				}
			}
			/*****/
	$tbl .= '<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_HAZARD_CLASSIFICATION .'</td>'
		.		'<td width="75%" colspan="3" align="center"><table width="100%" border="1" cellspacing="0" cellpadding="2">'
		.			'<tbody>'
		.			'<tr align="left">'
		.				'<td style="'.$label2.'" width="30%">'. _LBL_PHY_HAZARD .'</td>'
		.				'<td width="70%">'. ( ($str_catPH!="") ? $str_catPH : "-" ) .'</td>'
		.			'</tr>'
		.			'<tr align="left">'
		.				'<td style="'.$label2.'">'. _LBL_HEALTH_HAZARD .'</td>'
		.				'<td>'. ( ($str_catHH!="") ? $str_catHH : "-" ) .'</td>'
		.			'</tr>'
		.			'<tr align="left">'
		.				'<td style="'.$label2.'">'. _LBL_ENV_HAZARD .'</td>'
		.				'<td>'. ( ($str_catEH!="") ? $str_catEH : "-" ) .'</td>'
		.			'</tr>'
		.			'</tbody>'
		.		'</table></td>'
		.	'</tr>'
		;
				
				// if($lvlID!=7 && !empty($totalSupply) ){
				if($lvlID!=7){
	$tbl .= '<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_TOTAL_QUANTITY_SUPPLY .'</td>'
		.		'<td colspan="3">'. ( !empty($totalSupply) ? $totalSupply : "0.0" ) ." ".  _LBL_TONNE_YEAR .'</td>'
		;
	$tbl .= '</tr>';
				}
				// if($lvlID!=6 && !empty($totalUse) ){
				if($lvlID!=6){
	$tbl .= '<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_TOTAL_QUANTITY_USE .'</td>'
		.		'<td colspan="3">'. ( !empty($totalUse) ? $totalUse : "0.0" ) ." ".  _LBL_TONNE_YEAR .'</td>'
		;
	$tbl .= '</tr>';
				}
	/*
	$tbl .= '<tr>'
		.		'<td width="20%" style="'.$label2.'">'. _LBL_DATE_NOLONGER .'</td>'
		.		'<td colspan="3">'. (($detail["StopDate"]!="")?func_ymd2dmy($detail["StopDate"]):"-") .'</td>'
		.	'</tr>'
		;
	*/
		}

	$tbl .= '</tbody>'
		. '</table>'
		. '</center>'
		;
	// echo $tbl;die;
	$pdf->writeHTML($tbl, false, false, false, false, "");
	/*****/
	$pdf->SetDisplayMode("fullpage", "single");
	//$pdf->Output();
	$fileName = "CIMS_CRT_02.". str_replace("/","_",$subID).".pdf";
	$pdf->Output($fileName,"I");
	/*****/
?>

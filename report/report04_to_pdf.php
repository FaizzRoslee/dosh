<?php
	//============================================================
	// FILE BEGIN 
	// filename: report03_to_pdf.php
	// description: overall summary report convert to pdf
	//============================================================
        set_time_limit(0);                   // ignore php timeout

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
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	$uLevelID = $_SESSION['user']['Level_ID'];
	// if($uLevelID==5) exit();
	//==========================================================
 	$_tcpdf = 'tcpdf/';
	require_once($_tcpdf.'config/lang/eng.php');
	require_once($_tcpdf.'tcpdf.php');
	//==========================================================
	if(isset($_GET['lvlType'])){ 
            $lvlType = $_GET['lvlType'];
        }
        else { $lvlType = ''; }
	//==========================================================
	if(isset($_GET['state'])){
            $state = $_GET['state'];
	}
	else { $state = ''; }
	//==========================================================
	//$lvlName = ($lvlType!='') ? strtoupper(_get_StrFromCondition('sys_Level','Level_Name','Level_ID',$lvlType)) : '';
	
	$lvlName = "";
        
	if($lvlType=="all"){
            
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .",";
		}
		$lvlName = substr_replace($lvlName,"",-2);
		
                $levelType = "6,7,8";
                
                
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		$levelType = $lvlType;
	}
	$titleRpt = strtoupper(_LBL_REPORT) .': '. strtoupper(_LBL_LIST_SUPPLIER);
	//==========================================================
	$titleSupp = strtoupper(_LBL_SUPPLIER_TYPE) .': '. strtoupper($lvlName);
	//==========================================================
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	$text_valign_mid = 'vertical-align:middle;';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleSupp, $titleYear, $sys_config;
			
			$this->Ln(10);
			$this->SetFont('helvetica','',8);
			//====================================================
			$tblh = '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="center"><img src="'.$sys_config['images_path'].'Jata-Negara.jpg" height="50" width="70" border="0" /></td>
				</tr>
				<tr>
					<td align="center">'.strtoupper(_LBL_DOSH).'</td>
				</tr>
				<tr>
					<td align="center">'.strtoupper(_LBL_CIMS).'</td>
				</tr>
				<tr>
					<td align="center">'.$titleRpt.'</td>
				</tr>
				<tr>
					<td align="center">'.$titleSupp.'</td>
				</tr>
			</table>
				';
			$this->writeHTML($tblh, true, false, true, false, '');
			//====================================================
			//$this->Ln(2);
			//Ensure table header is output	
			parent::Header();
		}
		
		public function Footer(){
			$this->SetFont('helvetica','',8);
			$this->SetY(-10);
			$this->Cell(0,3,_LBL_PRINTED_ON .': '.date('d-m-Y'),0,0,'L');
			$this->SetY(-10);
			//Page number
			$this->Cell(0,3,_LBL_PAGE .' '. $this->PageNo().'/{nb}',0,0,'R');
		}
	}
	//============================================================================================================
	$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 

	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(5, 58); //left, top, right

	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	//============================================================================================================
	// set font
	$pdf->SetFont('helvetica', '', 7.5);

	$pdf->AliasNbPages(); 
	$pdf->AddPage();

	$label = 'color:#000000; background-color:#F1F1F1; font-weight:bold;';
	$label2 = 'color:#000000; background-color:#EFEFEF; font-weight:bold;';
	$label3 = 'color:#000000; background-color:#D7D7D7; font-weight:bold;';
	$label4 = 'color:#000000; background-color:#BFBFBF; font-weight:bold;';
	
	$tbl = '
	<center>
	
	<table border="1" width="100%" cellspacing="0" cellpadding="2">
		<thead>
		<tr style="'.$label.'">
			<td width="4%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_BIL .'</td>
			<td width="25%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_SUPPLIER_NAME .'</td>
			<td width="40%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_ADDRESS .'</td>
			<td width="31%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_CONTACT_DETAIL .'</td>
		</tr>
		</thead>
		<tbody>
		';
		
		$arr_state = explode(',',$state);
                
		foreach($arr_state as $value){
		
	$tbl .= '
		<tr class="contents">
			<td colspan="4" width="100%" style="'. $label .'">'. _get_StrFromCondition("sys_State","State_Name","State_ID",$value) .'</td>
		</tr>
		';
		
			/*****/
			$dataColumns = "u.Usr_ID,u.Level_ID,u.Active,uc.Company_Name,uc.Address_Reg,uc.Postcode_Reg,uc.City_Reg,"
						. "uc.Contact_Name,uc.Contact_Designation,uc.Contact_Mobile_No,uc.Contact_Email";
			$dataTable = "sys_User_Client uc";
			$dataJoin = array("sys_User u"=>"uc.Usr_ID = u.Usr_ID");
			$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
			$dataWhere["AND u.Active"] = "= 1";
			$dataWhere["AND u.isDeleted"] = "= 0";
			if($lvlType!="") {
                            $dataWhere["AND u.Level_ID"] = "IN (".quote_smart($levelType,false).")";
                            //$dataWhere["AND u.Level_ID"] = $levelType;
			}
			$dataOrder = "u.Level_ID ASC, Company_Name ASC";
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
			/*****/
			$bil = 0;
			foreach($arrayData as $data){
				$bil++;
				
				$comp = $data['Company_Name']; 
                $add1 = $data['Address_Reg']; 
				$add2 		= $data['Postcode_Reg'];
				$add3 		= $data['City_Reg'];
                                
				$compAdd	= $add1."<br/>".$add2." ".$add3;
                                
				$contact	= _LBL_NAME .": ". strtoupper($data["Contact_Name"]) ."<br />"
							. _LBL_CONTACT_NO .": ". $data["Contact_Mobile_No"] ."<br />"
							. _LBL_EMAIL .": ". $data["Contact_Email"];
							
				$lvlID = $data["Level_ID"];
				$lvlName = "";
				if($lvlType=="all")
					$lvlName = "<br /><b>[". _getLabel($lvlID,$arrLevel) ."]</b>";
	$tbl .= '
		<tr class="contents">
			<td width="4%"  style="'. $text_align_center.$text_valign_mid .'">'. $bil .'</td>
			<td width="25%" style="'. $text_valign_mid .'">'.strtoupper($comp).$lvlName.'</td>
			<td width="40%" style="'. $text_valign_mid .'">'. $compAdd .'</td>
			<td width="31%" style="'. $text_valign_mid .'">'. $contact .'</td>
		</tr>
		';
		
			}
			if($bil==0){
	$tbl .= '
		<tr class="contents">
			<td width="100%" colspan="4"  style="'. $text_align_center.$text_valign_mid .'">'. _LBL_NO_RECORD .'</td>
		</tr>
		';
			
			}
		}
			
	$tbl .= '
		</tbody>
	</table>
	</center>
		';
	
	//$tbl .= error_reporting(E_ALL);
	//echo $tbl;
	$pdf->writeHTML($tbl, true, false, true, false, '');
	//========================================================================================================
	//$pdf->SetDisplayMode('fullpage', 'single');
	$pdf->SetDisplayMode('fullpage');
	$pdf->Output();
	//$fileName = 'Report04.pdf';
	//$pdf->Output($fileName,'D');
	//====================================================================================================================
?>
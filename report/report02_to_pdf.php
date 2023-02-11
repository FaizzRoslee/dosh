<?php
	//============================================================
	// FILE BEGIN 
	// filename: report02_to_pdf.php
	// description: overall summary report convert to pdf
	//============================================================
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
	
	
	// linuxhouse__20220710
	// start penambahbaikan - 12_penjanaan laporan - v001: function
	
	include_once $sys_config['includes_path'].'set_time_and_memory_limits.php';// checks execution time and memory limit
	include_once $sys_config['includes_path'].'func_shutdown_timeout.php';// checks timeout errors
	
	// end penambahbaikan - 12_penjanaan laporan - v001: function
	/*****/
	
	
	
	
	// linuxhouse__20220710
	// start penambahbaikan - 12_penjanaan laporan - v001: check timeouts
	
	// make values global
	global $max_execution_time;
	global $memory_limit;
    // set maximum time execution
	ini_set('max_execution_time', $max_execution_time); // seconds
	// set memory limit
	ini_set('memory_limit', $memory_limit); // bytes or MB or 1G
	// check for timeouts
    register_shutdown_function('func_shutdown_timeout');
    
	// end penambahbaikan - 12_penjanaan laporan - v001: check timeouts
	
	
	
	
	//==========================================================
 	$_tcpdf = 'tcpdf/';
	require_once($_tcpdf.'config/lang/eng.php');
	require_once($_tcpdf.'tcpdf.php');
	//==========================================================
	if(isset($_GET['rptType'])) $rptType = $_GET['rptType'];
	else $rptType = '';
	//==========================================================
	if(isset($_GET['lvlType'])) $lvlType = $_GET['lvlType'];
	else $lvlType = '';
	//==========================================================
	if(isset($_GET['rptYear'])) $rptYear = $_GET['rptYear'];
	else $rptYear = date('Y');
	//==========================================================
	if(isset($_GET['state'])) $state = $_GET['state'];
	else $state = '';
	//==========================================================

	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
		}
		$lvlName = substr_replace($lvlName,"",-2);
		$levelType = "6,7,8";
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		$levelType = $lvlType;
		/*
		$lvlName = "";
		foreach($arrLevel as $detail){
			if($detail["value"]==$lvlType){
				$lvlName = "[". strtoupper($detail["label"]) ."]"; 
				break; 
			}
		}
		*/
	}
	$lvlName = "[". strtoupper($lvlName) ."]"; 
	
	$langdesc = (($fileLang=="may.php")?"_may":"");
	//==========================================================
	$rptName = '';
	foreach($arrReport as $detail){
		if($detail['value']==$rptType) $rptName = strtoupper($detail['label']); 
	}
	//$rptName = 'OVERALL SUMMARY REPORT '. $lvlName;
	$titleRpt = strtoupper(_LBL_SUMMARY) .': '.$rptName.' '. $lvlName;
	$titleYear = strtoupper(_LBL_YEAR) .': '. ($rptYear-1);
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	//==========================================================
	if($_SESSION['user']['Level_ID']!=5)
		$rptNo = '05';
	else
		$rptNo = '04';
	$rptFilename = 'CIMS/RPT/'.$rptNo.'/02';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleYear, $sys_config,$rptFilename;
			
			$this->Ln(10);
			$this->SetFont('helvetica','',8);
			//====================================================
			$tbl = '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="right">'.$rptFilename.'</td>
				</tr>
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
					<td align="center">'.$titleYear.'</td>
				</tr>
			</table>
				';
			$this->writeHTML($tbl, true, false, false, false, '');
			//====================================================
			//$this->Ln(2);
			//Ensure table header is output	
			parent::Header();
		}
		
		public function Footer(){
			$this->SetFont('helvetica','',10);
			$this->SetY(-10);
			$this->Cell(0,3,_LBL_AS_OF .': '.date('d-m-Y'),0,0,'L');
			$this->SetY(-10);
			//Page number
			$this->Cell(0,3,_LBL_PAGE .' '. $this->PageNo().'/{nb}',0,0,'R');
		}
	}
	//============================================================================================================
	$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 

	$pdf->SetAutoPageBreak(true, 20);
	$pdf->SetMargins(15, 58); //left, top, right

	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	//============================================================================================================
	// set font
	$pdf->SetFont('helvetica', '', 7);

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
			<td width="5%" style="'. $text_align_center .'" rowspan="2">'. _LBL_BIL .'</td>
			<td width="20%" style="'. $text_align_center .'" rowspan="2"><nobr>'. _LBL_COMPANY_NAME .'</nobr></td>
			<td width="15%" style="'. $text_align_center .'" rowspan="2"><nobr>'. _LBL_COMPANY_TYPE .'</nobr></td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_STATUS .'</td>
			<td width="50%" style="'. $text_align_center .'" colspan="3">'. _LBL_CHEMICAL .'</td>
		</tr>
		<tr style="'.$label.'">
			<td width="5%" style="'. $text_align_center .'">'. _LBL_NUMBER .'</td>
			<td width="35%" style="'. $text_align_center .'">'. _LBL_NAME .'</td>
			<td width="10%" style="'. $text_align_center .'">'. _LBL_CAS_NO .'</td>
			<!--<td width="10%" style="'. $text_align_center .'">'. _LBL_NOLONGER .'</td>-->
		</tr>
		</thead>
		';
			$arr_state = explode(',',$state);
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'. $text_align_center .'"';
			$td_mid_right = ' style="vertical-align:middle;'. $text_align_right .'"';
			//=========================================
			$countExist = 0;
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
	$tbl .= '
		<tr style="'.$label.'"><td colspan="9" width="100%">'. (_get_StrFromCondition('sys_State','State_Name','State_ID',$value)) .'</td></tr>
		';
					//=====================================================
					$dataColumns = 'u.Usr_ID,u.Level_ID,u.Active';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
					if($levelType!='')
						$dataWhere["AND Level_ID"] = "IN (". quote_smart($levelType,false) .")";
					$dataOrder		= "Level_ID ASC, Company_Name ASC";
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
					$bil = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						$lID = $detail['Level_ID'];
						$aID = $detail['Active'];
						//=====================================================
						$dataColumns1 = '*';
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND sd.isDeleted" => "= 0"
										,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
									);
						$dataOrder1 = '';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
						$countData1 = count($arrayData1);
						//=====================================================
						$new_chemIDs = array();
						//=====================================================
						foreach($arrayData1 as $detail1){
							//=====================================================
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							//=====================================================
							foreach($arrayChem as $indChem => $detChem){
								if(! in_array($detChem,$new_chemIDs) ) array_push($new_chemIDs,$detChem);
							}
							//=====================================================
						}
						//=====================================================
						$rowspan = count($new_chemIDs);
						if($rowspan==0 || $rowspan=='') $rowspan = 1;
						$countFirst = 0;
						//=====================================================
						foreach($new_chemIDs as $key1 => $detail1){
							$countFirst++;
							//=====================================================
							$td_rowspan = ($rowspan>1) ? ' rowspan="'.$rowspan.'"' : '';
	$tbl .= '
		<tr>
		';
							if($key1==0){
								$bil++;
	$tbl .= '
			<td width="5%" '. $td_rowspan . $td_mid_center .'>'. $bil .'</td>
			<td width="20%" '. $td_rowspan . $td_mid .'>'. (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID)) .'</td>
			<td width="15%" '. $td_rowspan . $td_mid_center .'>'. (_get_StrFromCondition('sys_Level','Level_Name'. $langdesc,'Level_ID',$lID)) .'</td>
			<td width="10%" '. $td_rowspan . $td_mid_center .'>
		';
								foreach($arrStatusAktif as $stat){
									if($stat['value']==$aID) 
										$tbl .= $stat['label']; 
								}
	$tbl .= '
			</td>
		';
							}
	$tbl .= '
			<td width="5%" '. $td_mid_center .'>'. $countFirst .'</td>
			<td width="35%" '. $td_mid .'>'. (_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$detail1['Chemical_ID'])) .'</td>
			<td width="10%" '. $td_mid_center .'>'. (_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$detail1['Chemical_ID'])) .'</td>
			<!--<td width="10%" '. $td_mid_center .'>'. (!empty($detail1['DateStop'])?_LBL_YES:'-') .'</td>-->
		</tr>
		';
						}
					}
					if($bil==0){
	$tbl .= '
		<tr height="30">
			<td colspan="9" width="100%" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
					}
				}
			}
			if($countExist==0){
	$tbl .= '
		<tr height="30">
			<td colspan="9" width="100%" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
			}
	$tbl .= '
	</table>
	</center>
		';
		//echo $tbl;die;
	$pdf->writeHTML($tbl, true, false, false, false, '');
	//========================================================================================================
	//$pdf->SetDisplayMode('fullpage', 'single');
    $pdf->SetDisplayMode('fullpage');
	
	
	
	
    // linuxhouse__20220710
	// start penambahbaikan - 12_penjanaan laporan - v001: prevent TCPDF ERROR: Some data has already been output, can't send PDF file
	
    ob_end_clean();// clean any content of the output buffer
    
    // end penambahbaikan - 12_penjanaan laporan - v001: prevent TCPDF ERROR: Some data has already been output, can't send PDF file
    
    
    
    
	$pdf->Output();
	//$fileName = str_replace("/","_",$rptFilename) .'.pdf';
	//$pdf->Output($fileName,'D');
	//====================================================================================================================
?>

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
	//==========================================================
	$uLevelID = $_SESSION['user']['Level_ID'];
	if($uLevelID<6) exit();
	//==========================================================
 	$_tcpdf = 'tcpdf/';
	require_once($_tcpdf.'config/lang/eng.php');
	require_once($_tcpdf.'tcpdf.php');
	//==========================================================
	if(isset($_GET['rptType'])) $rptType = $_GET['rptType'];
	else $rptType = '';
	//==========================================================
	if(isset($_GET['rptYear'])) $rptYear = $_GET['rptYear'];
	else $rptYear = date('Y');
	//==========================================================
	$lvlName = '';
	foreach($arrLevel as $detail){
		if($detail['value']==$uLevelID){
			$lvlName = '['. strtoupper($detail['label']) .']'; 
			break; 
		}
	}
	//==========================================================
	$rptName = '';
	foreach($arrReport as $detail){
		if($detail['value']==$rptType) $rptName = strtoupper($detail['label']); 
	}
	$suppName = strtoupper($_SESSION['user']['name']);
	$titleRpt = strtoupper(_LBL_SUMMARY) .': '.$rptName;
	$titleSupp = strtoupper(_LBL_SUPPLIER_NAME) .': '.$suppName.' '. $lvlName;
	$titleYear = strtoupper(_LBL_YEAR) .': '. ($rptYear-1);
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	//==========================================================
	if($uLevelID==6) $rptNo = '01';
	elseif($uLevelID==7) $rptNo = '02';
	elseif($uLevelID==8) $rptNo = '03';
	else $rptNo = '00';
	$rptFilename = 'CIMS/RPT/'.$rptNo.'/02';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleSupp, $titleYear, $sys_config,$rptFilename;
			
			$this->Ln(10);
			$this->SetFont('helvetica','',8);
			//====================================================
			$tbl = '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="right">'.$rptFilename.'</td>
				</tr>
				<tr>
					<td align="center"><img src="'.$sys_config['images_path'].'jata.gif" height="50" width="70" border="0" /></td>
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

	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(15, 64); //left, top, right

	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	//============================================================================================================
	// set font
	$pdf->SetFont('helvetica', '', 9);

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
			<td width="100%" style="'. $text_align_center .'" colspan="3">'. _LBL_CHEMICAL .'</td>
		</tr>
		<tr style="'.$label.'">
			<td width="5%" style="'. $text_align_center .'">'. _LBL_NUMBER .'</td>
			<td width="70%" style="'. $text_align_center .'">'. _LBL_NAME .'</td>
			<td width="25%" style="'. $text_align_center .'">'. _LBL_CAS_NO .'</td>
			<!--<td width="10%" style="'. $text_align_center .'">'. _LBL_NOLONGER .'</td>-->
		</tr>
		</thead>
		';
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'. $text_align_center .'"';
			$td_mid_right = ' style="vertical-align:middle;'. $text_align_right .'"';
			//=========================================
			$bil = 0;
			$uID = $_SESSION['user']['Usr_ID'];
			$lID = $uLevelID;
			$aID = _get_StrFromCondition('sys_User','Active','Usr_ID',$uID);
			//=========================================
			$dataColumns1 = '*';
			$dataTable1 = 'tbl_Submission s';
			$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
			$dataWhere1 = array(
							"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)"
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
				$bil++;
				$countFirst++;
				//=====================================================
				$td_rowspan = ($rowspan>1) ? ' rowspan="'.$rowspan.'"' : '';
	$tbl .= '
		<tr>
		';

	$tbl .= '
			<td width="5%" '. $td_mid_center .'>'. $countFirst .'</td>
			<td width="70%" '. $td_mid .'>'. (_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$detail1['Chemical_ID'])) .'</td>
			<td width="25%" '. $td_mid_center .'>'. (_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$detail1['Chemical_ID'])) .'</td>
			<!--<td width="10%" '. $td_mid_center .'>'. (!empty($detail1['DateStop'])?_LBL_YES:'-') .'</td>-->
		</tr>
		';
			}
			if($bil==0){
	$tbl .= '
		<tr height="30">
			<td colspan="3" width="100%" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
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
	$pdf->SetDisplayMode('fullpage', 'single');
	//$pdf->Output();
	$fileName = str_replace("/","_",$rptFilename) .'.pdf';
	$pdf->Output($fileName,'D');
	//====================================================================================================================
?>
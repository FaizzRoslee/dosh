<?php
	//============================================================
	// FILE BEGIN 
	// filename: report03_to_pdf.php
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
	// if($uLevelID==5) exit();
	//==========================================================
 	$_tcpdf = 'tcpdf/';
	require_once($_tcpdf.'config/lang/eng.php');
	require_once($_tcpdf.'tcpdf.php');
	//==========================================================
	$titleRpt = strtoupper(_LBL_REPORT) .': '. strtoupper(_LBL_ENFORCEMENT_CONTACT_INFO);
	//==========================================================
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	$text_valign_mid = 'vertical-align:middle;';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleYear, $sys_config;
			
			$this->Ln(10);
			$this->SetFont('helvetica','',8);
			//====================================================
			$tbl = '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
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
			</table>
				';
			$this->writeHTML($tbl, true, false, false, false, '');
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
	$pdf->SetMargins(10, 55); //left, top, right

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
			<td width="5%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_BIL .'</td>
			<td width="25%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_STATE .'</td>
			<td width="70%" style="'. $text_align_center . $text_valign_mid .'">'. _LBL_CONTACT_INFO .'</td>
		</tr>
		</thead>
		<tbody>
		';
		
	$i = 0;
	$dStates = _get_arraySelect("sys_State","State_ID","State_Name",array("AND isDeleted"=>"= 0"));
	foreach($dStates as $state){
		$i++;
		
		$dCols 	= "PIC_Name,PIC_Email,PIC_ContactNo";
		$dTable = "sys_User_Staff";
		$dJoin 	= array("sys_User"=>"$dTable.Usr_ID = sys_User.Usr_ID");
		$dWhere = array("AND State_ID" => "= ".quote_smart($state["value"]),
						"AND Level_ID" => "= 5",
						"AND Active" => "= 1",
						"AND isDeleted" => "= 0"
					);
		$arData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere); 
		//=============================================================================
		$contact = "";
		foreach($arData as $detail){
			$name	 = $detail["PIC_Name"];
			$email	 = $detail["PIC_Email"];
			$phoneno = $detail["PIC_ContactNo"];
			
			if(empty($name)) $name = "-";
			if(empty($email)) $email = "-";
			if(empty($phoneno)) $phoneno = "-";
			
			$contact .= _LBL_NAME .": <b>$name</b><br />"
					. _LBL_EMAIL .": <b>$email</b><br />"
					. _LBL_CONTACT_NO .": <b>$phoneno</b><br />"
					;
		}

		$tbl .= '
		<tr class="contents">
			<td width="5%" style="'. $text_align_center .'">'. $i .'</td>
			<td width="25%">'. $state["label"] .'</td>
			<td width="70%">'. (!empty($contact)?$contact:"-") .'</td>
		</tr>
		';
	}
			
	$tbl .= '
		</tbody>
	</table>
	</center>
		';
		
	// echo $tbl;
	$pdf->writeHTML($tbl, true, false, false, false, '');
	//========================================================================================================
	$pdf->SetDisplayMode('fullpage', 'single');
	//$pdf->Output();
	$fileName = 'Report05.pdf';
	$pdf->Output($fileName,'D');
	//====================================================================================================================
?>
<?php
	//============================================================
	// FILE BEGIN 
	// filename: regCert_pdf.php
	// description: registration certification
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
 	$_tcpdf = 'tcpdf/';
	require_once($_tcpdf.'config/lang/eng.php');
	require_once($_tcpdf.'tcpdf.php');
	//==========================================================
	if(isset($_GET['Usr_ID'])) $Usr_ID = $_GET['Usr_ID'];
	else $Usr_ID = 0;
	//==========================================================	
	$regID 	= '';
	$regDate = '';
	$actDate = '';
	$coName	= '';
	$coNo	= '';
	$lvlName = '';
	//===============================
	if($Usr_ID!=0){
		$dataColumns = 'u.*, uc.*';
		$dataTable = 'sys_User u';
		$dataJoin = array('sys_User_Client uc'=>'u.Usr_ID = uc.Usr_ID');
		$dataWhere = array("AND u.Usr_ID" => "= ".quote_smart($Usr_ID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//print_r($arrayData);
		//==========================================================
		foreach($arrayData as $detail){
			$regID 	= strtoupper($detail['Reg_ID']);
			$regDate = func_ymd2dmy($detail['RecordDate']);
			$actDate = func_ymd2dmy($detail['ActivationDate']);
			$coName	= strtoupper($detail['Company_Name']);
			$coNo	= strtoupper($detail['Company_No']);
			$lvlID = $detail['Level_ID'];
		}
	}
	//==========================================================
	class MYPDF extends TCPDF{
		public function Footer(){
			global $sys_config;
			$this->SetFont('helvetica','',9);
			$this->SetY(-30);
			//====================================================
			$tbl = '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
					<td align="right"><img src="'.$sys_config['images_path'].'DOSH_COP.jpg" width="60" border="0" /></td>
				</tr>
			</table>
				';
			$this->writeHTML($tbl, true, false, false, false, '');
		}
	}
	//==========================================================
	$pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, false, 'UTF-8', false); 

	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(20, 20); //left, top, right
	$pdf->setPrintHeader(false);

	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	//==========================================================
	// set font
	$pdf->SetFont('helvetica', '', 9);
	$pdf->AliasNbPages(); 
	$pdf->AddPage();
	
	$lvlName = '';
	foreach($arrLevel as $detail){
		if($detail['value']==$lvlID){
			$lvlName = $detail['label']; 
			break; 
		}
	}
	
	$tbl = '
	<center>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<tr><td width="100%" style="text-align:right"><i>CIMS/CRT/01</i></td></tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
		<tbody>
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
			<td align="center">'.strtoupper(_LBL_CERT_TITLE).'</td>
		</tr>
		</tbody>
	</table>
	<br />
	<table style="border:1px solid" cellspacing="1" cellpadding="3" width="100%">
		<tbody>
		<tr>
			<td><table border="0" cellspacing="1" cellpadding="10" width="100%">
				<tbody>
				<tr>
					<td width="30%"><label><strong>'. _LBL_CERT_REG_ID .'</strong></label></td>
					<td width="70%">: <label>'. $regID .'</label></td>
				</tr>
				<tr>
					<td><label><strong>'. _LBL_CERT_REG_DATE .'</strong></label></td>
					<td>: <label>'. $regDate .'</label></td>
				</tr>
				<tr>
					<td><label><strong>'. _LBL_CERT_ACTIVATION_DATE .'</strong></label></td>
					<td>: <label>'. $actDate .'</label></td>
				</tr>
				<tr>
					<td><label><strong>'. _LBL_CERT_COMP_NAME .'</strong></label></td>
					<td>: <label>'. $coName .'</label></td>
				</tr>
				<tr>
					<td><label><strong>'. _LBL_CERT_COMP_REG_NO .'</strong></label></td>
					<td>: <label>'. $coNo .'</label></td>
				</tr>
				<tr>
					<td><label><strong>'. _LBL_CERT_TYPE_SUPPIER .'</strong></label></td>
					<td>: <label>'. $lvlName .'</label></td>
				</tr>
				<tr>
					<td colspan="2"><hr size="3" color="black" /><br /><label>'. _LBL_CERT_SUBJ_INFO . _LBL_CERT_ORG_INFO .'</label></td>
				</tr>
				</tbody>
			</table></td>
		</tr>
		</tbody>
	</table>
	</center>
		';
	//	echo $tbl;die;
	$pdf->writeHTML($tbl, false, false, false, false, '');
	//========================================================================================================
	$pdf->SetDisplayMode('fullpage', 'single');
	//$pdf->Output();
	$fileName = 'CIMS_CRT_01.'. str_replace("/","_",$regID).'.pdf';
	$pdf->Output($fileName,'I');
	//====================================================================================================================
?>
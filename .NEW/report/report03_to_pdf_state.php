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
	if(isset($_GET['chemType'])) $chemType = $_GET['chemType'];
	else $chemType = '';
	//==========================================================
	if(isset($_GET['status'])) $status = $_GET['status'];
	else $status = '';
	//==========================================================
	if(isset($_GET['rptYear'])) $rptYear = $_GET['rptYear'];
	else $rptYear = date('Y');
	//==========================================================
	if(isset($_GET['state'])) $state = $_GET['state'];
	else $state = '';
	//==========================================================
	//$lvlName = ($lvlType!='') ? '['. strtoupper(_get_StrFromCondition('sys_Level','Level_Name','Level_ID',$lvlType)) .']' : '';
	$lvlName = '';
	foreach($arrLevel as $detail){
		if($detail['value']==$lvlType){
			$lvlName = strtoupper($detail['label']); 
			break; 
		}
	}
	$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
	$typeName = ($chemType!='') ? strtoupper(_get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$chemType)) : '';
	//$typeName = ($chemType!='') ? strtoupper(_get_StrFromCondition('tbl_Chemical_Type','Type_Name','Type_ID',$chemType)) : '';
	//==========================================================
	$rptName = '';
	foreach($arrReportSub as $detail){
		if($detail['value']==$rptType){
			$rptName = strtoupper($detail['label']) .' '. $typeName ; 
			break; 
		}
	}
	$titleRpt = strtoupper(_LBL_REPORT) .': '.$rptName;
	//==========================================================
	if($rptType=='rpt04') $arrDetail = $arrRptStatusR;
	else $arrDetail = $arrRptStatusN;
	$sName = '';
	foreach($arrDetail as $detail){
		if($detail['value']==$status){
			$sName = strtoupper($detail['label']); 
			break; 
		}
	}
	$titleStatus = strtoupper(_LBL_STATUS).': '.$sName;
	//==========================================================
	$titleSupp = strtoupper(_LBL_SUPPLIER_TYPE) .': '.$typeName;
	//==========================================================
	$titleYear = strtoupper(_LBL_YEAR).': '. ($rptYear-1);
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	$text_valign_mid = 'vertical-align:middle;';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleStatus, $titleSupp, $titleYear, $sys_config;
			
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
				<tr>
					<td align="center">'.$titleStatus.'</td>
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
			$this->SetFont('helvetica','',9);
			$this->SetY(-10);
			$this->Cell(0,3,_LBL_AS_OF .': '.date('d-m-Y'),0,0,'L');
			$this->SetY(-10);
			//Page number
			$this->Cell(0,3,_LBL_PAGE .' '. $this->PageNo().'/{nb}',0,0,'R');
		}
	}
	//============================================================================================================
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 

	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(8, 68); //left, top, right

	//set some language-dependent strings
	$pdf->setLanguageArray($l); 
	//============================================================================================================
	// set font
	$pdf->SetFont('helvetica', '', 8);

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
			<td width="3%" style="'. $text_align_center . $text_valign_mid .'" rowspan="2">'. _LBL_BIL .'</td>
		';	
				$maxColspan = 7;
				$width_100_90 = '90%';
				$trade_chemical = _LBL_TRADE_PRODUCT;
				if($chemType==2){
	$tbl .= '
			<td width="10%" style="'. $text_align_center . $text_valign_mid .'" rowspan="2">'. _LBL_PRODUCT_NAME .'</td>
		';	
					$maxColspan = 8;
					$width_100_90 = '100%';
					$trade_chemical = _LBL_CHEMICAL_NAME;
				}
	$tbl .= '
			<td width="7%" style="'. $text_align_center . $text_valign_mid .'" rowspan="2">'. _LBL_PHYSICAL_FORM .'</td>
			<td width="13%" style="'. $text_align_center .'" rowspan="2">'. $trade_chemical .'</td>
			<td width="7%" style="'. $text_align_center .'" rowspan="2">'. _LBL_CAS_NO .'</td>
			<td width="60%" style="'. $text_align_center .'" colspan="3">'. _LBL_HAZARD_CLASSIFICATION .'</td>
		</tr>
		<tr style="'.$label.'">
			<td width="20%" style="'. $text_align_center .'">'. _LBL_PHY .'</td>
			<td width="20%" style="'. $text_align_center .'">'. _LBL_HEALTH .'</td>
			<td width="20%" style="'. $text_align_center .'">'. _LBL_ENV .'</td>
		</tr>
		</thead>
		';
			$arr_state = explode(',',$state);
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			//=========================================	
			$countExist = 0;
			$grand_totalSupply = 0;
			$grand_totalUse = 0;
			$showExport = 0;
			
			$dateStart 	= _get_StrFromCondition("sys_SubmitDate","Date_Start","YEAR(Date_Last)",$rptYear);
			$dateLast 	= _get_StrFromCondition("sys_SubmitDate","Date_Last","YEAR(Date_Last)",$rptYear);
			
			//=========================================
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
	$tbl .= '
		<tr class="contents">
			<td colspan="'. $maxColspan .'" width="'. $width_100_90 .'" style="'. $label .'">'. (_get_StrFromCondition('sys_State','State_Name','State_ID',$value)) .'</td>
		</tr>
		';
					//=====================================================
					$dataColumns = 'u.Usr_ID,u.Level_ID,u.Active,uc.Company_Name';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
					if($lvlType!='')
						$dataWhere["AND Level_ID"] = "= ".quote_smart($lvlType);
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
					$bil = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						$lID = $detail['Level_ID'];
						$cName = $detail['Company_Name'];
						//=====================================================
						if($status=='31') $status .= ',32';
						elseif($status=='41') $status .= ',42';
						$dataColumns1 = "Detail_EntityTo,Detail_EntityFrom,Detail_ID,Form_ID,Detail_ProductName,"
									.	" Category_ID_ph,Category_ID_hh,Category_ID_eh,Detail_TotalSupply,Detail_TotalUse";
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID","tbl_Submission_Submit ss"=>"s.Submission_ID = ss.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),
										"AND s.Status_ID"=>"IN (".$status.")",
										"AND s.Type_ID" =>"= ".quote_smart($chemType),
										// ,"AND DATE_FORMAT(s.RecordDate,'%Y')"=>"= ".quote_smart($rptYear)
										"AND (ss.SubmitTime >= ". quote_smart($dateStart) => "AND ss.SubmitTime <= ". quote_smart($dateLast)  .")"
									);
						$dataOrder1 = '';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
						//extractArray($arrayData1);
						$countData1 = count($arrayData1);
						//=====================================================
						$new_chemIDs = array();
						$new_rowspan1 = 0;
						//=====================================================
						$rowspan = count($new_chemIDs);
						if($rowspan==0 || $rowspan=='') $rowspan = 1;
						$countFirst = 0;
						//=====================================================
						//extractArray($arrayData1);
						$sub_totalSupply = 0;
						$sub_totalUse = 0;
						foreach($arrayData1 as $key1 => $detail1){
							$bil++;
							$countFirst++;
							//=====================================================
							$rowspan = max($countData1,$new_rowspan1);
							if($rowspan==0 || $rowspan=='') $rowspan = 1;
							//=====================================================
							$entityTo 		= $detail1['Detail_EntityTo'];
							$ex_entityTo 	= explode(',',$entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1['Detail_EntityFrom'];
							$ex_entityFrom 	= explode(',',$entityFrom);
							$count_entityFrom	= 0;
							//=====================================================
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							$count_arrayChem = count($arrayChem);
							//=====================================================
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
							//=====================================================
							for($i=0;$i<$rowspan1;$i++){
								$td_rowspan = ($rowspan1>1) ? ' rowspan="'.$rowspan1.'"' : '';
								if($i==0 && $key1==0){
	$tbl .= '
		<tr>
			<td colspan="'. $maxColspan .'" width="'. $width_100_90 .'" style="'. $label2 .'">- <i>'. _LBL_SUPPLIER_NAME .':</i> '. $cName .'</td>
		</tr>
		';
								}
	$tbl .= '
		<tr>
		';
								if($i==0){
	$tbl .= '
			<td width="3%" '. $td_rowspan .' style="'. $text_align_center.$text_valign_mid .'">'. $bil .'</td>
		';
				if($chemType==2){
	$tbl .= '
			<td width="10%" '. $td_rowspan .' style="'. $text_valign_mid .'">'. $detail1['Detail_ProductName'] .'</td>
		';
				}
	$tbl .= '
			<td width="7%" '. $td_rowspan .' style="'. $text_align_center.$text_valign_mid .'">'. (_get_StrFromCondition('tbl_Chemical_Form','Form_Name','Form_ID',$detail1['Form_ID'])) .'</td>
		';
								}
	$tbl .= '
			<td width="13%" style="'. $text_valign_mid .'">'. (isset($arrayChem[$i]['Chemical_ID'])?nl2br(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID'])):'') .'</td>
			<td width="7%" style="'. $text_align_center.$text_valign_mid .'">'. (isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):'') .'</td>
		';
								if($i==0){
	$tbl .= '
			<td width="20%" '. $td_rowspan .' style="'. $text_valign_mid .'">
		';
				$ex_hazCat = explode(',',$detail1['Category_ID_ph']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
									);
							$tbl .=	';<br />';
						}
					}
				}
	$tbl .= '
			</td>
			<td width="20%" '. $td_rowspan .' style="'. $text_valign_mid .'">
		';
				$ex_hazCat = explode(',',$detail1['Category_ID_hh']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								);
							$tbl .= ';<br />';
						}
					}
				}
	$tbl .= '
			</td>
			<td width="20%" '. $td_rowspan .' style="'. $text_valign_mid .'">
		';
				$ex_hazCat = explode(',',$detail1['Category_ID_eh']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								);
							$tbl .= ';<br />';
						}
					}
				}
	$tbl .= '
			</td>
		';
								}
	$tbl .= '
		</tr>
		';
							}
						}
					}
					if($bil==0){
	$tbl .= '
		<tr height="30">
			<td colspan="'. $maxColspan .'" width="'. $width_100_90 .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
					}
					$showExport += $bil;
				}
			}
			if($countExist==0){
	$tbl .= '
		<tr height="30">
			<td colspan="'. $maxColspan .'" width="'. $width_100_90 .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
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
	$fileName = 'Report03.pdf';
	$pdf->Output($fileName,'D');
	//====================================================================================================================
?>
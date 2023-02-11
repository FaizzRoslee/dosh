<?php
	//============================================================
	// FILE BEGIN 
	// filename: report01_to_pdf.php
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
	$uLevelID = $_SESSION['user']['Level_ID'];
	//$uLevelID = 5;
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
	if(isset($_GET['rptYear'])) $rptYear = $_GET['rptYear'];
	else $rptYear = '';
	//==========================================================
	if(isset($_GET['state'])) $state = $_GET['state'];
	else $state = '';
	//==========================================================
	//$lvlName = ($lvlType!='') ? _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$lvlType) : '';
	$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
	$typeName = ($chemType!='') ? strtoupper(_get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$chemType)) : '';
	//$typeName = ($chemType!='') ? strtoupper(_get_StrFromCondition('tbl_Chemical_Type','Type_Name','Type_ID',$chemType)) : '';
	//==========================================================
	$lvlName = '';
	foreach($arrLevel as $detail){
		if($detail['value']==$lvlType){
			$lvlName = $detail['label']; 
			break; 
		}
	}
	//==========================================================
	$rptName = '';
	foreach($arrReport as $detail){
		if($detail['value']==$rptType) $rptName = strtoupper($detail['label']); 
	}
	$titleRpt = strtoupper(_LBL_SUMMARY) .': '.$rptName.' ['. $typeName .']';
	//==========================================================
	$titleSupp = strtoupper(_LBL_SUPPLIER_TYPE) .': '.strtoupper($lvlName);
	//==========================================================
	$titleYear = strtoupper(_LBL_YEAR) .': '. date('Y');
	$text_align_center = 'text-align:center;';
	$text_align_right = 'text-align:right;';
	//==========================================================
	$rptFilename = 'CIMS/RPT/05/01';
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
					<td align="right">'. $rptFilename .'</td>
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

	$pdf->SetAutoPageBreak(true, 20);
	$pdf->SetMargins(10, 67); //left, top, right

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
			<td width="4%" style="'. $text_align_center .'">'. _LBL_BIL .'</td>
		';
			$minus = 1;
			$trade_chemical = _LBL_TRADE_PRODUCT;
			if($chemType==2){//15
	$tbl .= '
			<td width="15%" class="label" style="'. $text_align_center .'">'. _LBL_PRODUCT_NAME .'</td>
		';
				$minus = 0;
				$trade_chemical = _LBL_CHEMICAL_NAME;
			}
	$tbl .= '
			<td width="25%" style="'. $text_align_center .'">'. $trade_chemical .'</td>
			<td width="10%" style="'. $text_align_center .'">'. _LBL_CAS_NO .'</td>
			<td width="34%" style="'. $text_align_center .'"><nobr>'. _LBL_HAZARD_CLASSIFICATION .'</nobr></td>
			<td width="12%" style="'. $text_align_center .'">'. _LBL_SUBMISSION_DATE .'</td>
		</tr>
		</thead>
		';
			$arr_state = explode(',',$state);
			$bil = 0;
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'. $text_align_center .'"';
			$td_mid_right = ' style="vertical-align:middle;'. $text_align_right .'"';
			//=========================================	
			$grand_totalSupply = 0;
			$grand_totalUse = 0;
			//=========================================
			$wid_all = ($chemType==2) ? '100%' : '85%';
			$countExist = 0;
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
	$tbl .= '
		<tr style="'.$label.'"><td colspan="'. (6-$minus) .'" width="'. $wid_all .'">'. (_get_StrFromCondition('sys_State','State_Name','State_ID',$value)) .'</td></tr>
		';
					//=====================================================
					$dataColumns = 'u.Usr_ID';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere = array("AND uc.State_Reg"=>"= ".quote_smart($value),"AND Level_ID"=>"= ".quote_smart($lvlType));
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
					$countFirst = 0;
					//=====================================================
					$sum_totalSupply = 0;
					$sum_totalUse = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						//=====================================================
						$subselect = "(SELECT Sub_Record_Date FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status IN (11,12) ORDER BY Sub_Record_Date DESC LIMIT 1)";
						$dataColumns1 = '*,'.$subselect.' AS submitDate';
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND Type_ID"=>"= ".quote_smart($chemType)
										,"AND DATE_FORMAT(".$subselect.",'%Y')"=>"= ".quote_smart($rptYear)
									);
						$dataOrder1 = ' '.$subselect.' DESC';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1,$dataOrder1); 
						$countData1 = count($arrayData1);
						//=====================================================
						$new_rowspan1 = 0;
						//=====================================================
						$sub_totalSupply = 0;
						$sub_totalUse = 0;
						//=====================================================
						foreach($arrayData1 as $key => $detail1){
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
							$new_rowspan1 += $rowspan1;
							//=====================================================
						}
						foreach($arrayData1 as $key => $detail1){
							$countFirst++;
							$bil++;
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

								if($key==0&&$i==0){
	$tbl .= '
		<tr style="'.$label.'"><td colspan="'. (6-$minus) .'" width="'. $wid_all .'">:: '. (($lvlName!='')?$lvlName:_LBL_COMPANY_NAME) .' - '. (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID)) .'</td></tr>
		';
								}
	$tbl .= '
		<tr>
		';
								if($i==0){
	$tbl .= '
			<td width="4%" '. $td_rowspan . $td_mid_center .'>'. $bil .'</td>
		';
								}
/*
								if($key==0&&$i==0){
	$tbl .= '
			<td width="17%" '. (($rowspan>1) ? 'rowspan="'. $rowspan .'"' : '') . $td_mid .'>'. (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID)) .'</td>
		';
								}
*/
								if($i==0&&$chemType==2){
	$tbl .= '
			<td width="15%" '. $td_rowspan . $td_mid .'>'. $detail1['Detail_ProductName'] .'</td>
		';
								}
	$tbl .= '
			<td width="25%" '. $td_mid .'>'. (isset($arrayChem[$i]['Chemical_ID'])?nl2br(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID'])):'&nbsp;') .'</td>
			<td width="10%" '. $td_mid_center .'>'. (isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):'&nbsp;') .'</td>
		';
								if($i==0){
									//=========================================
									$phCategory = $detail1['Category_ID_ph'];
									$hhCategory = $detail1['Category_ID_hh'];
									$ehCategory = $detail1['Category_ID_eh'];
									//=========================================
									$comb_hazCat = $phCategory;
									$comb_hazCat .= ($hhCategory!='') ? ( ($comb_hazCat!='') ? ','.$hhCategory : '' ) :'';
									$comb_hazCat .= ($ehCategory!='') ? ( ($comb_hazCat!='') ? ','.$ehCategory : '' ) :'';
									//=========================================
									$totalSupply = $detail1['Detail_TotalSupply'];
									$sub_totalSupply += $totalSupply;
									//=========================================
	$tbl .= '
			<td width="34%" '. $td_rowspan . $td_mid .'>
		';
				$ex_comb_hazCat = explode(',',$comb_hazCat);
				$tyIDs = 0;
				foreach($ex_comb_hazCat as $catID){
					$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
								array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
								array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
					if($nohazard==0){
						$tyID = _get_StrFromCondition('tbl_Hazard_Category','Type_ID','Category_ID',$catID);
						if($tyID!=$tyIDs){
							if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
							elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
							elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
							else{ $clName = '-'; }
							$tbl .= '<strong><u>'.$clName.'</u>:-</strong><br />';
						}
						$tyIDs = $tyID;
						$tbl .= _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								).';<br />';
					}
				}
	$tbl .= '
			</td>
			<td width="12%" '. $td_rowspan . $td_mid_center .'>'. (func_ymd2dmy($detail1['submitDate'])) .'</td>
		';
								}
	$tbl .= '
		</tr>
		';
							}
						}
					}
					//================================================state looping================================================\\
					if($countFirst==0){
	$tbl .= '
		<tr height="30">
			<td width="'. $wid_all .'" colspan="'. (6-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
					}
					//================================================state looping================================================\\
				}
			}
			if($countExist==0){
	$tbl .= '
		<tr height="30">
			<td width="'. $wid_all .'" colspan="'. (6-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
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
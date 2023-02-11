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
	if($uLevelID==5) exit();
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
	$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
	$typeName = ($chemType!='') ? strtoupper(_get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$chemType)) : '';
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
	$rptFilename = 'CIMS/RPT/04/01';
	//============================================================================================================
	class MYPDF extends TCPDF{
		public function Header(){
			global $titleRpt, $titleSupp, $titleYear, $sys_config, $rptFilename;
			
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
	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); 

	$pdf->SetAutoPageBreak(true, 15);
	$pdf->SetMargins(10, 66); //left, top, right

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
	
	//=========================================
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
	$wid_all = '100%';
	$countExist = 0;	
	//=========================================
	
	if($lvlType!=8){
	
	$tbl = '
	<center>
	<table border="1" width="100%" cellspacing="0" cellpadding="2">
		<thead>
		<tr style="'.$label.'">
			<td width="3%" style="'. $text_align_center .'" rowspan="2">'. _LBL_BIL .'</td>
		';
		$minus = 1;
		$width1 = '42';
		// $width2 = '49%';
		$trade_chemical = _LBL_TRADE_PRODUCT;
		if($chemType==2){
	$tbl .= '
			<td width="12%" class="label" style="'. $text_align_center .'" rowspan="2">'. _LBL_PRODUCT_NAME .'</td>
		';
			$minus = 0;
			$width1 = '30';
			// $width2 = '55%';
			$trade_chemical = _LBL_CHEMICAL_NAME;
		}
	$tbl .= '
			<td width="14%" style="'. $text_align_center .'" rowspan="2">'. $trade_chemical .'</td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_CAS_NO .'</td>
			<td width="'.$width1.'%" style="'. $text_align_center .'" colspan="3"><nobr>'. _LBL_HAZARD_CLASSIFICATION .'</nobr></td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_APPROVED_DATE .'</td>
		';
		if($lvlType!=7){
	$tbl .= '
			<td width="8%" style="'. $text_align_center .'" rowspan="2">'. _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')</td>
			<td width="13%" style="'. $text_align_center .'" rowspan="2">'. _LBL_LIST_SUPPLIED_ENTITY .'</td>
		';
		}
		if($lvlType!=6){
	$tbl .= '
			<td width="8%" style="'. $text_align_center .'" rowspan="2">'. _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')</td>
			<td width="13%" style="'. $text_align_center .'" rowspan="2">'. _LBL_LIST_SUPPLIER_ENTITY .'</td>
		';
		}
	$tbl .= '
		</tr>
		<tr style="'.$label.'">
			<td width="'.($width1/3).'%" style="'. $text_align_center .'"><nobr>'. _LBL_PHY .'</nobr></td>
			<td width="'.($width1/3).'%" style="'. $text_align_center .'"><nobr>'. _LBL_HEALTH .'</nobr></td>
			<td width="'.($width1/3).'%" style="'. $text_align_center .'"><nobr>'. _LBL_ENV .'</nobr></td>
		</tr>
		</thead>
		';

			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
	$tbl .= '
		<tr style="'.$label.'"><td colspan="'. (10-$minus) .'" width="'. $wid_all .'">'. (_get_StrFromCondition('sys_State','State_Name','State_ID',$value)) .'</td></tr>
		';
					//=====================================================
					$dataColumns = 'u.Usr_ID';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere = array("AND uc.State_Reg"=>"= ".quote_smart($value),"AND Level_ID"=>"= ".quote_smart($lvlType));
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
					//=====================================================
					$sum_totalSupply = 0;
					$sum_totalUse = 0;
					$countSecond = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						//=====================================================
						$dataColumns1 = '*';
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND Type_ID"=>"= ".quote_smart($chemType)
										,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
									);
						$dataOrder1 = 'ApprovedDate ASC';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
						$countData1 = count($arrayData1);
						//=====================================================
						$new_rowspan1 = 0;
						//=====================================================
						$sub_totalSupply = 0;
						$sub_totalUse = 0;
						$countFirst = 0;
						//=====================================================
						foreach($arrayData1 as $key => $detail1){
							$countFirst++;
							$bil++;
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
		<tr style="'.$label.'">
			<td colspan="'. (10-$minus) .'" width="'. $wid_all .'">:: '. (($lvlName!='')?$lvlName:_LBL_COMPANY_NAME) .' - '. (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID)) .'</td>
		</tr>
		';
								}
	$tbl .= '
		<tr>
		';
								if($i==0){
	$tbl .= '
			<td width="3%" '. $td_rowspan . $td_mid_center .'>'. $bil .'</td>
		';
								}
								if($i==0&&$chemType==2){
	$tbl .= '
			<td width="12%" '. $td_rowspan . $td_mid .'>'. $detail1['Detail_ProductName'] .'</td>
		';
								}
	$tbl .= '
			<td width="14%" '. $td_mid .'>'. (isset($arrayChem[$i]['Chemical_ID'])?nl2br(func_fn_escape(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID']))):'&nbsp;') .'</td>
			<td width="10%" '. $td_mid_center .'>'. (isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):'&nbsp;') .'</td>
		';
								if($i==0){
									//=========================================
									$phCategory = $detail1['Category_ID_ph'];
									$hhCategory = $detail1['Category_ID_hh'];
									$ehCategory = $detail1['Category_ID_eh'];
									//=========================================
									// $comb_hazCat = $phCategory;
									// $comb_hazCat .= ($hhCategory!='') ? ( ($comb_hazCat!='') ? ','.$hhCategory : '' ) :'';
									// $comb_hazCat .= ($ehCategory!='') ? ( ($comb_hazCat!='') ? ','.$ehCategory : '' ) :'';
	$tbl .= '
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_phCategory = explode(',',$phCategory);
				foreach($ex_phCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_hhCategory = explode(',',$hhCategory);
				foreach($ex_hhCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_ehCategory = explode(',',$ehCategory);
				foreach($ex_ehCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="10%" '. $td_rowspan . $td_mid_center .'>'. (func_ymd2dmy($detail1['ApprovedDate'])) .'</td>
		';
									
									//=========================================
									$totalSupply = $detail1['Detail_TotalSupply'];
									$sub_totalSupply += $totalSupply;
									//=========================================
									if($lvlType!=7 && $uLevelID!=5){ 
	$tbl .= '
			<td width="8%" '. $td_rowspan . $td_mid_right .'>'. (number_format($totalSupply,1)) .'</td>
			<td width="13%" '.  $td_rowspan . $td_mid .'>
		';
										foreach($ex_entityTo as $coID){
											if($coID!=''){
												$tbl 	.= _get_StrFromCondition('tbl_Client_Company','Company_Name','Company_ID',$coID)
														.' ('. ((_get_StrFromCondition('tbl_Client_Company','Active','Company_ID',$coID)==1)?_LBL_ACTIVE:_LBL_INACTIVE) .');<br />';
											}
										}
	$tbl .= '
			&nbsp;</td>
		';
									}
									//=========================================
									$totalUse = $detail1['Detail_TotalUse'];
									$sub_totalUse += $totalUse;
									//=========================================
									if($lvlType!=6 && $uLevelID!=5){ 
	$tbl .= '
			<td width="8%" '. $td_rowspan . $td_mid_right .'>'. (number_format($totalUse,1)) .'</td>
			<td width="13%" '. $td_rowspan . $td_mid .'>
		';
										foreach($ex_entityFrom as $coID){
											if($coID!=''){
												$tbl 	.= _get_StrFromCondition('tbl_Client_Company','Company_Name','Company_ID',$coID)
														.' ('. ((_get_StrFromCondition('tbl_Client_Company','Active','Company_ID',$coID)==1)?_LBL_ACTIVE:_LBL_INACTIVE) .');<br />';
											}
										}
	$tbl .= '
			&nbsp;</td>
		';				
									}
								}
	$tbl .= '
		</tr>
		';
							}
						}

						//================================================company looping================================================\\
						if($countFirst>0){ 
							if($uLevelID!=5){
	$tbl .= '
		<tr style="'.$label2.'">
			<td width="69%" colspan="'. (7-$minus) .'" '.$td_mid_right.'>&nbsp;</td>
		';
								$sum_totalSupply += $sub_totalSupply;
								if($lvlType!=7){
	$tbl .= '
			<td width="10%" '. $td_mid_right .'>'. _LBL_TOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sub_totalSupply,1)) .'</td>
		';
								}
								$sum_totalUse += $sub_totalUse;
								if($lvlType!=6){
							
	$tbl .= '
			<td width="10%" '. $td_mid_right .'>'. _LBL_TOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sub_totalUse,1)) .'</td>
		';
								}
	$tbl .= '
			<td width="13%">&nbsp;</td>
		</tr>
		';
							}
						}
						$countSecond += $countFirst;
						//================================================company looping================================================\\
						
					}
					
					//================================================state looping================================================\\
					if($countSecond>0){ 
						if($uLevelID!=5){
	$tbl .= '
		<tr style="'.$label3.'">
			<td width="69%" colspan="'. (7-$minus) .'" '. $td_mid_right .'>&nbsp;</td>
		';
							$grand_totalSupply += $sum_totalSupply;
							if($lvlType!=7){
	$tbl .= '
			<td width="10%" '. $td_mid_right .'>'. _LBL_SUBTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sum_totalSupply,1)) .'</td>
		';
							}
							$grand_totalUse += $sum_totalUse;
							if($lvlType!=6){
	$tbl .= '		
			<td width="10%" '. $td_mid_right .'>'. _LBL_SUBTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sum_totalUse,1)) .'</td>
		';
							}
	$tbl .= '
			<td width="13%">&nbsp;</td>
		</tr>
		';
						}
					}else{
	$tbl .= '
		<tr height="30">
			<td width="'. $wid_all .'" colspan="'. (10-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
					}
					//================================================state looping================================================\\

				}
			}
			
			//================================================================================================\\
			if($bil>0){
				if($uLevelID!=5){
	$tbl .= '
		<tr style="'.$label4.'">
			<td width="69%" colspan="'. (7-$minus) .'" '. $td_mid_right .'>&nbsp;</td>
		';
						if($lvlType!=7){
	$tbl .= '
			<td width="10%" '. $td_mid_right .'>'. _LBL_GRANDTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($grand_totalSupply,1)) .'</td>
		';
						}
						if($lvlType!=6){
	$tbl .= '			
			<td width="10%" '. $td_mid_right .'>'. _LBL_GRANDTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($grand_totalUse,1)) .'</td>
		';
						}
	$tbl .= '
			<td width="13%">&nbsp;</td>
		</tr>
		';
				}
			}
			if($countExist==0){
	$tbl .= '
		<tr height="30">
			<td width="10%" colspan="'. (10-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
			}
			//================================================================================================\\
			
	$tbl .= '
	</table>
	</center>
		';
		
	} else {

	$tbl = '
	<center>
	<table border="1" width="100%" cellspacing="0" cellpadding="2">
		<thead>
		<tr style="'.$label.'">
			<td width="3%" style="'. $text_align_center .'" rowspan="2">'. _LBL_BIL .'</td>
		';
		$minus = 1;
		$width1 = 33;
		$width2 = 2;
		$trade_chemical = _LBL_TRADE_PRODUCT;
		if($chemType==2){
	$tbl .= '
			<td width="8%" class="label" style="'. $text_align_center .'" rowspan="2">'. _LBL_PRODUCT_NAME .'</td>
		';
			$minus = 0;
			$width1 = 27;
			$width2 = 0;
			$trade_chemical = _LBL_CHEMICAL_NAME;
		}
	$tbl .= '
			<td width="'. (8 + $width2) .'%" style="'. $text_align_center .'" rowspan="2">'. $trade_chemical .'</td>
			<td width="8%" style="'. $text_align_center .'" rowspan="2">'. _LBL_CAS_NO .'</td>
			<td width="'.$width1.'%" style="'. $text_align_center .'" colspan="3"><nobr>'. _LBL_HAZARD_CLASSIFICATION .'</nobr></td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_APPROVED_DATE .'</td>
			<td width="8%" style="'. $text_align_center .'" rowspan="2">'. _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')</td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_LIST_SUPPLIED_ENTITY .'</td>
			<td width="8%" style="'. $text_align_center .'" rowspan="2">'. _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')</td>
			<td width="10%" style="'. $text_align_center .'" rowspan="2">'. _LBL_LIST_SUPPLIER_ENTITY .'</td>
		</tr>
		<tr style="'.$label.'">
			<td width="'. ($width1/3) .'%" style="'. $text_align_center .'"><nobr>'. _LBL_PHY .'</nobr></td>
			<td width="'. ($width1/3) .'%" style="'. $text_align_center .'"><nobr>'. _LBL_HEALTH .'</nobr></td>
			<td width="'. ($width1/3) .'%" style="'. $text_align_center .'"><nobr>'. _LBL_ENV .'</nobr></td>
		</tr>
		</thead>
		';
		foreach($arr_state as $value){
			if($value!=0){
				$countExist++;
	$tbl .= '
		<tr style="'.$label.'"><td colspan="'. (12-$minus) .'" width="'. $wid_all .'">'. (_get_StrFromCondition('sys_State','State_Name','State_ID',$value)) .'</td></tr>
		';
				//=====================================================
				$dataColumns = 'u.Usr_ID';
				$dataTable = 'sys_User_Client uc';
				$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
				$dataWhere = array("AND uc.State_Reg"=>"= ".quote_smart($value),"AND Level_ID"=>"= ".quote_smart($lvlType));
				$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
				//=====================================================
				$sum_totalSupply = 0;
				$sum_totalUse = 0;
				$countSecond = 0;
				//=====================================================
				foreach($arrayData as $detail){
					$uID = $detail['Usr_ID'];
					//=====================================================
					$dataColumns1 = '*';
					$dataTable1 = 'tbl_Submission s';
					$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
					$dataWhere1 = array(
									"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND Type_ID"=>"= ".quote_smart($chemType)
									,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
								);
					$dataOrder1 = 'ApprovedDate ASC';
					$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
					$countData1 = count($arrayData1);
					//=====================================================
					$new_rowspan1 = 0;
					//=====================================================
					$sub_totalSupply = 0;
					$sub_totalUse = 0;
					$countFirst = 0;
					//=====================================================
					foreach($arrayData1 as $key => $detail1){
						$countFirst++;
						$bil++;
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
		<tr style="'.$label.'">
			<td colspan="'. (12-$minus) .'" width="'. $wid_all .'">:: '. (($lvlName!='')?$lvlName:_LBL_COMPANY_NAME) .' - '. (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID)) .'</td>
		</tr>
		';
							}
	$tbl .= '
		<tr>
		';
							if($i==0){
$tbl .= '
		<td width="3%" '. $td_rowspan . $td_mid_center .'>'. $bil .'</td>
	';
							}
							if($i==0&&$chemType==2){
$tbl .= '
		<td width="8%" '. $td_rowspan . $td_mid .'>'. $detail1['Detail_ProductName'] .'</td>
	';
							}
	$tbl .= '
			<td width="'. (8 + $width2) .'%" '. $td_mid .'>'. (isset($arrayChem[$i]['Chemical_ID'])?nl2br(func_fn_escape(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID']))):'&nbsp;') .'</td>
			<td width="8%" '. $td_mid_center .'>'. (isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):'&nbsp;') .'</td>
		';
							if($i==0){
								//=========================================
								$phCategory = $detail1['Category_ID_ph'];
								$hhCategory = $detail1['Category_ID_hh'];
								$ehCategory = $detail1['Category_ID_eh'];
								//=========================================
								// $comb_hazCat = $phCategory;
								// $comb_hazCat .= ($hhCategory!='') ? ( ($comb_hazCat!='') ? ','.$hhCategory : '' ) :'';
								// $comb_hazCat .= ($ehCategory!='') ? ( ($comb_hazCat!='') ? ','.$ehCategory : '' ) :'';
								//=========================================
								$totalSupply = $detail1['Detail_TotalSupply'];
								$sub_totalSupply += $totalSupply;
								//=========================================
	$tbl .= '
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_phCategory = explode(',',$phCategory);
				foreach($ex_phCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_hhCategory = explode(',',$hhCategory);
				foreach($ex_hhCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="'.($width1/3).'%" '. $td_rowspan . $td_mid .'>
		';
				$ex_ehCategory = explode(',',$ehCategory);
				foreach($ex_ehCategory as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tbl .= (_get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
									)).';<br />
								';
						}
					}
				}
	$tbl .= '
			&nbsp;</td>
			<td width="10%" '. $td_rowspan . $td_mid_center .'>'. (func_ymd2dmy($detail1['ApprovedDate'])) .'</td>
		';
	$tbl .= '
			<td width="8%" '. $td_rowspan . $td_mid_right .'>'. (number_format($totalSupply,1)) .'</td>
			<td width="10%" '.  $td_rowspan . $td_mid .'>
		';
									foreach($ex_entityTo as $coID){
										if($coID!=''){
											$tbl 	.= _get_StrFromCondition('tbl_Client_Company','Company_Name','Company_ID',$coID)
													.' ('. ((_get_StrFromCondition('tbl_Client_Company','Active','Company_ID',$coID)==1)?_LBL_ACTIVE:_LBL_INACTIVE) .');<br />';
										}
									}
	$tbl .= '
			&nbsp;</td>
		';
									//=========================================
									$totalUse = $detail1['Detail_TotalUse'];
									$sub_totalUse += $totalUse;
									//=========================================
	$tbl .= '
			<td width="8%" '. $td_rowspan . $td_mid_right .'>'. (number_format($totalUse,1)) .'</td>
			<td width="10%" '. $td_rowspan . $td_mid .'>
		';
									foreach($ex_entityFrom as $coID){
										if($coID!=''){
											$tbl 	.= _get_StrFromCondition('tbl_Client_Company','Company_Name','Company_ID',$coID)
													.' ('. ((_get_StrFromCondition('tbl_Client_Company','Active','Company_ID',$coID)==1)?_LBL_ACTIVE:_LBL_INACTIVE) .');<br />';
										}
									}
	$tbl .= '
			&nbsp;</td>
		';				
							}
	$tbl .= '
		</tr>
		';
						}
					}

					//================================================company looping================================================\\
					if($countFirst>0){ 
						if($uLevelID!=5){
	$tbl .= '
		<tr style="'.$label2.'">
			<td width="54%" colspan="'. (7-$minus) .'" '.$td_mid_right.'>&nbsp;</td>
		';
							$sum_totalSupply += $sub_totalSupply;
							$sum_totalUse += $sub_totalUse;
	$tbl .= '
			<td width="10%" '. $td_mid_right .'>'. _LBL_TOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sub_totalSupply,1)) .'</td>
			<td width="10%" '. $td_mid_right .'>'. _LBL_TOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sub_totalUse,1)) .'</td>
			<td width="10%">&nbsp;</td>
		</tr>
		';
						}
					}
					$countSecond += $countFirst;
					//================================================company looping================================================\\
				}
					
				//================================================state looping================================================\\
				if($countSecond>0){ 
					if($uLevelID!=5){
						$grand_totalSupply += $sum_totalSupply;
						$grand_totalUse += $sum_totalUse;
	$tbl .= '
		<tr style="'.$label3.'">
			<td width="54%" colspan="'. (7-$minus) .'" '. $td_mid_right .'>&nbsp;</td>
			<td width="10%" '. $td_mid_right .'>'. _LBL_SUBTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sum_totalSupply,1)) .'</td>
			<td width="10%" '. $td_mid_right .'>'. _LBL_SUBTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($sum_totalUse,1)) .'</td>
			<td width="10%">&nbsp;</td>
		</tr>
		';
					}
				}else{
	$tbl .= '
		<tr height="30">
			<td width="'. $wid_all .'" colspan="'. (12-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
				}
				//================================================state looping================================================\\

			}
		}
		
		//================================================================================================\\
		if($bil>0){
			if($uLevelID!=5){
	$tbl .= '
		<tr style="'.$label4.'">
			<td width="54%" colspan="'. (7-$minus) .'" '. $td_mid_right .'>&nbsp;</td>
			<td width="10%" '. $td_mid_right .'>'. _LBL_GRANDTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($grand_totalSupply,1)) .'</td>
			<td width="10%" '. $td_mid_right .'>'. _LBL_GRANDTOTAL .'</td>
			<td width="8%" '. $td_mid_right .'>'. (number_format($grand_totalUse,1)) .'</td>
			<td width="10%">&nbsp;</td>
		</tr>
		';
			}
		}
		if($countExist==0){
	$tbl .= '
		<tr height="30">
			<td width="10%" colspan="'. (12-$minus) .'" '. $td_mid_center .'>'. _LBL_NO_RECORD .'</td>
		</tr>
		';
		}
		//================================================================================================\\

	$tbl .= '
	</table>
	</center>
		';

	}
	
	// echo $tbl;die;
	$pdf->writeHTML($tbl, true, false, false, false, '');
	//========================================================================================================
	$pdf->SetDisplayMode('fullpage', 'single');
	//$pdf->Output();
	$fileName = str_replace("/","_",$rptFilename) .'.pdf';
	$pdf->Output($fileName,'D');
	//====================================================================================================================
?>